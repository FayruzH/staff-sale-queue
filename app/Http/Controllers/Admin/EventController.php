<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Batch;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;


class EventController extends Controller
{
    private function thumbnailRules(): array
    {
        return ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
    }

    private function storeNormalizedThumbnail(UploadedFile $file): string
    {
        $raw = @file_get_contents($file->getRealPath());
        $src = $raw ? @imagecreatefromstring($raw) : false;

        // Fallback: keep upload flow working if server image library is unavailable.
        if (!$src) {
            return $file->store('events', 'public');
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        $targetW = 1600;
        $targetH = 1000; // 16:10 template
        $targetRatio = $targetW / $targetH;
        $srcRatio = $srcW / max(1, $srcH);

        if ($srcRatio > $targetRatio) {
            $cropH = $srcH;
            $cropW = (int) round($cropH * $targetRatio);
            $srcX = (int) floor(($srcW - $cropW) / 2);
            $srcY = 0;
        } else {
            $cropW = $srcW;
            $cropH = (int) round($cropW / $targetRatio);
            $srcX = 0;
            $srcY = (int) floor(($srcH - $cropH) / 2);
        }

        $canvas = imagecreatetruecolor($targetW, $targetH);
        $bg = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $bg);

        imagecopyresampled($canvas, $src, 0, 0, $srcX, $srcY, $targetW, $targetH, $cropW, $cropH);

        ob_start();
        imagejpeg($canvas, null, 88);
        $binary = ob_get_clean();

        imagedestroy($src);
        imagedestroy($canvas);

        $path = 'events/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function sanitizeDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        // Allow only basic formatting tags from rich text input.
        $clean = trim(strip_tags($description, '<p><br><strong><em><ul><ol><li><b><i><u>'));

        return $clean !== '' ? $clean : null;
    }

    private function generateEventCode(): string
    {
        $datePart = Carbon::now()->format('Ymd');

        do {
            $uniqueNumber = random_int(1000, 9999);
            $code = "EVT-{$datePart}-{$uniqueNumber}";
        } while (Event::where('code', $code)->exists());

        return $code;
    }

    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $sort = $request->string('sort')->toString() ?: 'nearest';

        $totalEvents = Event::count();
        $eventsQuery = Event::query();

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $eventsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhereDate('event_date', $q);
            });
        }

        if ($request->filled('status')) {
            $eventsQuery->where('status', $request->input('status'));
        }

        switch ($sort) {
            case 'name_az':
                $eventsQuery->orderBy('name', 'asc')->orderByDesc('id');
                break;
            case 'name_za':
                $eventsQuery->orderBy('name', 'desc')->orderByDesc('id');
                break;
            case 'farthest':
                // Kebalikan nearest: event masa depan terjauh dulu, lalu event lampau terlama.
                $eventsQuery
                    ->orderByRaw('CASE WHEN event_date >= ? THEN 0 ELSE 1 END', [$today])
                    ->orderByRaw('CASE WHEN event_date >= ? THEN event_date END DESC', [$today])
                    ->orderByRaw('CASE WHEN event_date < ? THEN event_date END ASC', [$today])
                    ->orderByDesc('id');
                break;
            default:
                // Default: event terdekat (upcoming terdekat dulu, lalu event lampau terbaru).
                $eventsQuery
                    ->orderByRaw('CASE WHEN event_date >= ? THEN 0 ELSE 1 END', [$today])
                    ->orderByRaw('CASE WHEN event_date >= ? THEN event_date END ASC', [$today])
                    ->orderByRaw('CASE WHEN event_date < ? THEN event_date END DESC', [$today])
                    ->orderByDesc('id');
                break;
        }

        $events = $eventsQuery->get();
        return view('admin.events.index', compact('events', 'totalEvents'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'location' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'thumbnail' => $this->thumbnailRules(),
            'event_date' => ['required','date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'break_start' => ['nullable'],
            'break_end' => ['nullable'],
            'batch_duration_min' => ['required','integer','min:1','max:180'],
            'gap_min' => ['required','integer','min:0','max:60'],
            'capacity_per_batch' => ['required','integer','min:1','max:500'],
        ]);

        $data['description'] = $this->sanitizeDescription($data['description'] ?? null);
        $data['location'] = ($loc = trim((string) ($data['location'] ?? ''))) !== '' ? $loc : null;
        $data['code'] = $this->generateEventCode();
        $data['status'] = 'draft';

        $thumbnailPath = null;

        if ($request->file('thumbnail')) {
            $thumbnailPath = $this->storeNormalizedThumbnail($request->file('thumbnail'));
        }

        $data['thumbnail'] = $thumbnailPath;

        $event = Event::create($data);

        return redirect()->route('admin.events.show', $event)->with('success', 'Event created!');
    }

    public function show(Event $event)
    {
        $event->load(['batches' => function($q){
            $q->orderBy('batch_number');
        }]);

        return view('admin.events.show', compact('event'));
    }

    public function start(Event $event)
    {
        $event->update(['status' => 'active']);
        return back()->with('success', 'Event started (Active).');
    }

    public function end(Event $event)
    {
        $event->update(['status' => 'ended']);
        return back()->with('success', 'Event ended (Turned Off).');
    }

    public function generateBatches(Event $event)
    {
        // For MVP: regenerate from scratch (safe for demo)
        DB::transaction(function () use ($event) {
            Batch::where('event_id', $event->id)->delete();

            $start = Carbon::parse($event->event_date.' '.$event->start_time);
            $end   = Carbon::parse($event->event_date.' '.$event->end_time);

            $breakStart = $event->break_start ? Carbon::parse($event->event_date.' '.$event->break_start) : null;
            $breakEnd   = $event->break_end ? Carbon::parse($event->event_date.' '.$event->break_end) : null;

            $duration = (int) $event->batch_duration_min;
            $gap      = (int) $event->gap_min;

            $batchNo = 1;
            $cursor = $start->copy();

            while (true) {

                if ($breakStart && $breakEnd && $cursor->gte($breakStart) && $cursor->lt($breakEnd)) {
                    $cursor = $breakEnd->copy();
                }

                $batchStart = $cursor->copy();
                $batchEnd = $batchStart->copy()->addMinutes($duration);

                if ($batchEnd->gt($end)) {
                    break;
                }

                Batch::create([
                    'event_id' => $event->id,
                    'batch_number' => $batchNo,
                    'start_time' => $batchStart->format('H:i:s'),
                    'end_time' => $batchEnd->format('H:i:s'),
                    'capacity' => $event->capacity_per_batch,
                    'color_code'   => Batch::colorByIndex($batchNo - 1),
                    'status' => 'upcoming',
                ]);

                $batchNo++;
                $cursor = $batchEnd->copy()->addMinutes($gap);
            }
        });

        return back()->with('success', 'Batches generated!');
    }

    public function toggleAutoMode(Event $event)
    {
        $event->update(['is_auto_mode' => !$event->is_auto_mode]);
        return back()->with('success', 'Auto mode: ' . ($event->is_auto_mode ? 'ON' : 'OFF'));
    }

    public function resetDemo(Event $event)
    {
        DB::transaction(function () use ($event) {

            // delete registrations
            Registration::where('event_id', $event->id)->delete();

            // reset batches
            Batch::where('event_id', $event->id)->update([
                'status' => 'upcoming',
                'started_at' => null,
            ]);


        });

        return back()->with('success', 'Demo reset successful.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            "description" => 'nullable|string',
            'thumbnail' => $this->thumbnailRules(),
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',

            'is_auto_mode' => 'nullable|in:0,1',

            'batch_duration_min' => 'required|integer|min:1',
            'gap_min' => 'required|integer|min:0',
            'capacity_per_batch' => 'required|integer|min:1',

            'break_start' => 'nullable',
            'break_end' => 'nullable',
        ]);

        $data['description'] = $this->sanitizeDescription($data['description'] ?? null);
        $data['location'] = ($loc = trim((string) ($data['location'] ?? ''))) !== '' ? $loc : null;
        $thumbnailPath = $event->thumbnail;

        if ($request->file('thumbnail')) {
            if (!empty($event->thumbnail)) {
                Storage::disk('public')->delete($event->thumbnail);
            }
            $thumbnailPath = $this->storeNormalizedThumbnail($request->file('thumbnail'));
        }

        $data['thumbnail'] = $thumbnailPath;

        $event->update($data);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event updated.');
    }


    public function destroy(Event $event)
    {
        DB::transaction(function () use ($event) {
            // delete children first
            Registration::where('event_id', $event->id)->delete();
            Batch::where('event_id', $event->id)->delete();

            // delete event
            $event->delete();
        });

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }





}
