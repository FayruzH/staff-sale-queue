<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $q = trim((string) $request->query('q'));
        $batchId = $request->query('batch_id');
        $status = $request->query('status'); // ✅ NEW: pending / checked

        // ✅ base query (buat paginate + summary)
        $base = Registration::query()
            ->where('event_id', $event->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('employee_identifier', 'like', "%{$q}%")
                        ->orWhere('employee_name', 'like', "%{$q}%")
                        ->orWhere('queue_number', 'like', "%{$q}%");
                });
            })
            ->when($batchId, function ($query) use ($batchId) {
                $query->where('batch_id', $batchId);
            })
            ->when($status === 'pending', function ($query) {
                $query->whereNull('checked_in_at');
            })
            ->when($status === 'checked', function ($query) {
                $query->whereNotNull('checked_in_at');
            });

        // ✅ summary pakai query (bukan paginator)
        $total     = (clone $base)->count();
        $checkedIn = (clone $base)->whereNotNull('checked_in_at')->count();
        $remaining = $total - $checkedIn;

        // ✅ paginate (baru with relation)
        $registrations = (clone $base)
            ->with(['batch'])
            ->orderBy('batch_id')
            ->orderBy('queue_number')
            ->paginate(20);

        $registrations->appends($request->query());

        $batches = $event->batches()->orderBy('batch_number')->get();

        return view('admin.attendance.index', compact(
            'event',
            'registrations',
            'batches',
            'q',
            'batchId',
            'status',      // ✅ pass to blade
            'total',
            'checkedIn',
            'remaining'
        ));
    }

        public function checkIn(Request $request, Event $event, Registration $registration)
        {
            if ((int)$registration->event_id !== (int)$event->id) {
                abort(404);
            }

            if (is_null($registration->checked_in_at)) {
                $registration->update([
                    'checked_in_at' => now(),
                    'checked_in_by' => 'admin',
                ]);
            }

            return back()->with('success', 'Check-in sukses.');
        }



    public function undoCheckIn(Event $event, Registration $registration)
    {
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        // prevent undo kalau batch / event sudah lewat
        $eventEnd = \Carbon\Carbon::parse($event->event_date.' '.$event->end_time);
        $batchEnd = \Carbon\Carbon::parse($event->event_date.' '.$registration->batch->end_time);

        if (now()->greaterThan($eventEnd) || now()->greaterThan($batchEnd)) {
            return back()->with('error', 'Batch / event sudah lewat. Tidak bisa undo.');
        }

        $registration->update([
            'checked_in_at' => null,
            'checked_in_by' => null,
        ]);

        return back()->with('success', 'Check-in berhasil di-undo. Ticket aktif kembali.');
    }



    // SCAN (QR payload: event_id|queue_number) + backward compatible queue_number aja
    public function scanCheckIn(Request $request, Event $event)
    {
        $data = $request->validate([
            'queue_number' => ['required', 'string', 'max:60'], // muat "123|B01-001"
        ]);

        $raw = trim($data['queue_number']);

        // ✅ STRICT: wajib format "event_id|queue"
        if (!str_contains($raw, '|')) {
            return back()
                ->withInput()
                ->with('error', 'Format tidak valid.');
        }
        // Parse QR
        [$eventFromQr, $queue] = array_pad(explode('|', $raw, 2), 2, null);

        $eventFromQr = (int) trim((string) $eventFromQr);
        $queue = strtoupper(trim((string) $queue));

        if ($eventFromQr <= 0 || $queue === '') {
            return back()
                ->withInput()
                ->with('error', 'QR tidak valid. Silakan scan ulang QR dari halaman ticket.');
        }

        // ✅ QR event lain
        if ($eventFromQr !== (int) $event->id) {
            return back()
                ->withInput()
                ->with('error', "QR ini untuk event lain (QR event: {$eventFromQr}, halaman event: {$event->id}).");
        }

        // ✅ cari ticket by event + queue
        $reg = Registration::where('event_id', $event->id)
            ->where('queue_number', $queue)
            ->first();

        if (!$reg) {
            return back()
                ->withInput()
                ->with('error', "Ticket tidak ditemukan untuk event ini. Queue: {$queue}");
        }

        // ✅ anti double check-in
        if (!is_null($reg->checked_in_at)) {
            return back()
                ->with('warning', "ALREADY: {$reg->queue_number} - {$reg->employee_name} ({$reg->employee_identifier})")
                ->with('last_scanned_id', $reg->id);
        }

        // ✅ do check-in
        $reg->update([
            'checked_in_at' => now(),
            'checked_in_by' => 'admin', // atau auth()->id() kalau pakai auth
        ]);

        return back()
            ->with('success', "OK: {$reg->queue_number} - {$reg->employee_name} ({$reg->employee_identifier})")
            ->with('last_scanned_id', $reg->id);
    }



}
