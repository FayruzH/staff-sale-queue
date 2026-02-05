<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Event;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DisplayController extends Controller
{
    public function show(Event $event)
    {
        return view('display.event', compact('event'));
    }

    public function data(Event $event)
    {
        $payload = $this->buildPayload($event);
        return response()->json($payload);
    }

    //  ===== TICKET LIVE VIEW ======
    public function ticketLive(Registration $registration)
    {

        $registration->load(['event.batches', 'batch']);

        $event = $registration->event;
        $payload = $this->buildPayload($event);

        $tz = $payload['timezone'] ?? config('app.timezone');
        $now = Carbon::parse($payload['server_now'], $tz);

        $yourBatch = $registration->batch;

        $yourStatus = 'unknown';
        $etaText = '—';

        // hitung your status & eta
        if ($yourBatch) {
            $start = Carbon::parse($event->event_date . ' ' . $yourBatch->start_time, $tz);
            $end   = Carbon::parse($event->event_date . ' ' . $yourBatch->end_time, $tz);

            if ($end->lt($start)) $end->addDay();

            if ($now->lt($start)) {
                $yourStatus = 'upcoming';
                $etaText = $this->fmtHHMMSS($start->diffInSeconds($now));
            } elseif ($now->between($start, $end)) {
                $yourStatus = 'running';
                $etaText = $this->fmtHHMMSS($end->diffInSeconds($now));
            } else {
                $yourStatus = 'done';
                $etaText = '00:00:00';
            }
        }

        // YOUR BATCH payload
        $payload['your_batch'] = $yourBatch ? [
            'id' => $yourBatch->id,
            'batch_number' => $yourBatch->batch_number,
            'start_time' => $yourBatch->start_time,
            'end_time' => $yourBatch->end_time,
            'color_code' => $yourBatch->color_code,
            'status' => $yourBatch->status,
        ] : null;

        $payload['your_status'] = $yourStatus;
        $payload['eta_text'] = $etaText;

        $payload['your_status'] = match ($payload['your_status'] ?? 'unknown') {
            'upcoming' => 'waiting',
            'running'  => 'running',
            'done'     => 'ended',
            default    => 'unknown',
        };

        return response()->json($payload);

    }

    // ====== INTERNAL HELPERS ======

    private function buildPayload(Event $event): array
    {
        // Auto advance dulu biar status running/next selalu up-to-date
        $this->autoAdvance($event);

        // Load batches
        $event->load(['batches' => function ($q) {
            $q->orderBy('batch_number');
        }]);

        $running = $event->batches->firstWhere('status', 'running');
        $next    = $event->batches->firstWhere('status', 'upcoming');

        $tz = config('app.timezone');
        $now = now($tz);

        // ===== Phase calculation =====
        $phase = 'idle';

        if ($event->status === 'draft') {
            $phase = 'draft';
        } elseif ($event->status === 'ended') {
            $phase = 'ended';
        } elseif ($event->status !== 'active') {
            $phase = 'idle';
        } else {
            $inBreak = false;

            if ($event->break_start && $event->break_end) {
                $breakStart = Carbon::parse($event->event_date . ' ' . $event->break_start, $tz);
                $breakEnd   = Carbon::parse($event->event_date . ' ' . $event->break_end, $tz);

                if ($breakEnd->lt($breakStart)) $breakEnd->addDay();

                if ($now->between($breakStart, $breakEnd)) $inBreak = true;
            }

            if ($inBreak) {
                $phase = 'break';
            } elseif ($running) {
                $phase = 'running';
            } elseif ($next) {
                $phase = 'waiting_start';
            } else {
                $phase = 'idle';
            }
        }

        // ===== payload =====
        $payload = [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'status' => $event->status,
                'auto_mode' => (bool) $event->is_auto_mode,
                'event_date' => (string) $event->event_date,
            ],
            'server_now' => $now->toIso8601String(),
            'timezone' => $tz,
            'running_batch' => null,
            'next_batch' => null,

            'phase' => $phase,

            'break_window' => [
                'break_start' => $event->break_start,
                'break_end' => $event->break_end,
            ],
        ];

        // RUNNING payload
        if ($running) {
            $start = Carbon::parse($event->event_date . ' ' . $running->start_time, $tz);
            $end   = Carbon::parse($event->event_date . ' ' . $running->end_time, $tz);
            if ($end->lt($start)) $end->addDay();

            $durationSec = $end->diffInSeconds($start);

            $payload['running_batch'] = [
                'id' => $running->id,
                'batch_number' => $running->batch_number,
                'start_time' => $running->start_time,
                'end_time' => $running->end_time,
                'color_code' => $running->color_code,
                'status' => $running->status,
                'started_at' => $running->started_at
                    ? Carbon::parse($running->started_at, $tz)->toIso8601String()
                    : null,
                'duration_seconds' => $durationSec,
            ];
        }

        // NEXT payload
        if ($next) {
            $payload['next_batch'] = [
                'id' => $next->id,
                'batch_number' => $next->batch_number,
                'start_time' => $next->start_time,
                'end_time' => $next->end_time,
                'color_code' => $next->color_code,
                'status' => $next->status,
            ];
        }

        return $payload;
    }

    private function fmtHHMMSS(int $sec): string
    {
        $sec = max(0, $sec);
        $h = intdiv($sec, 3600);
        $m = intdiv($sec % 3600, 60);
        $s = $sec % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private function autoAdvance(Event $event): void
    {
        // Hanya jalan kalau event active & auto_mode true
        if ($event->status !== 'active' || !$event->is_auto_mode) {
            return;
        }

        DB::transaction(function () use ($event) {

            // 1) Cek apakah ada batch yang sedang running
            $running = Batch::where('event_id', $event->id)
                ->where('status', 'running')
                ->lockForUpdate()
                ->first();

            // 2) AUTO FIRST BATCH
            if (!$running) {
                $first = Batch::where('event_id', $event->id)
                    ->where('status', 'upcoming')
                    ->orderBy('batch_number')
                    ->lockForUpdate()
                    ->first();

                if ($first) {
                    $first->update([
                        'status' => 'running',
                        'started_at' => now(),
                    ]);
                }
                return;
            }

            // 3) Kalau running ada tapi started_at kosong
            if (!$running->started_at) {
                $running->update(['started_at' => now()]);
                return;
            }

            // 4) AUTO NEXT
            $tz = config('app.timezone');

            $start = Carbon::parse($event->event_date . ' ' . $running->start_time, $tz);
            $end   = Carbon::parse($event->event_date . ' ' . $running->end_time, $tz);
            if ($end->lt($start)) $end->addDay();

            $durationSec = $end->diffInSeconds($start);
            $finishedAt = Carbon::parse($running->started_at, $tz)->addSeconds($durationSec);

            if (now($tz)->lt($finishedAt)) return;

            // selesaiin running
            $running->update([
                'status' => 'done',
            ]);

            // start next upcoming
            $next = Batch::where('event_id', $event->id)
                ->where('status', 'upcoming')
                ->orderBy('batch_number')
                ->lockForUpdate()
                ->first();

            if ($next) {
                $next->update([
                    'status' => 'running',
                    'started_at' => now(),
                ]);
            }
        });
    }
}
