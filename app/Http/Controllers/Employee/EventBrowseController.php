<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventBrowseController extends Controller
{
    // Event listing and details
    public function index(Request $request)
    {
        $sort = $request->string('sort')->toString() ?: 'nearest';

        $baseQuery = Event::query()->where('status', 'active');
        $totalEvents = (clone $baseQuery)->count();
        $eventsQuery = clone $baseQuery;

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));

            $eventsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhereDate('event_date', $q)
                    ->orWhere('event_date', 'like', "%{$q}%");
            });
        }

        switch ($sort) {
            case 'farthest':
                $eventsQuery->orderBy('event_date', 'desc')->orderBy('id', 'desc');
                break;
            case 'name_az':
                $eventsQuery->orderBy('name', 'asc')->orderBy('event_date', 'asc');
                break;
            case 'name_za':
                $eventsQuery->orderBy('name', 'desc')->orderBy('event_date', 'asc');
                break;
            case 'nearest':
            default:
                $eventsQuery->orderBy('event_date', 'asc')->orderBy('id', 'asc');
                break;
        }

        $events = $eventsQuery->get();

        return view('employee.events.index', compact('events', 'totalEvents'));
    }

    public function show(Event $event)
    {
        abort_unless($event->status === 'active', 404);

        $batches = $event->batches()
            ->orderBy('batch_number')
            ->get()
            ->map(function ($b) {
                $b->registered_count = $b->registrations()->count();
                $b->remaining_slots  = max(0, $b->capacity - $b->registered_count);
                return $b;
            });

        return view('employee.events.show', compact('event', 'batches'));
    }

    public function registerForm(Event $event, Batch $batch)
    {
        abort_unless($event->status === 'active', 404);
        abort_unless($batch->event_id === $event->id, 404);

        $registeredCount = $batch->registrations()->count();
        $remainingSlots  = max(0, $batch->capacity - $registeredCount);

        return view('employee.events.register', compact('event', 'batch', 'registeredCount', 'remainingSlots'));
    }

    public function registerSubmit(Request $request, Event $event, Batch $batch)
    {
        abort_unless($event->status === 'active', 403);
        abort_unless($batch->event_id === $event->id, 404);

        $data = $request->validate([
            'employee_id'   => ['required', 'string', 'max:50'],
            'employee_name' => ['required', 'string', 'max:255'],
        ]);

        $employeeId   = $data['employee_id'];
        $employeeName = $data['employee_name'];

        session(['employee_id' => $employeeId]);

        $result = DB::transaction(function () use ($event, $batch, $employeeId, $employeeName) {

            $existing = Registration::where('event_id', $event->id)
                ->where('employee_identifier', $employeeId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return ['type' => 'existing', 'registration' => $existing];
            }

            $lockedBatch = Batch::where('id', $batch->id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBatch->status !== 'upcoming') abort(422, 'Batch is not available.');

            $count = Registration::where('batch_id', $lockedBatch->id)
                ->lockForUpdate()
                ->count();

            if ($count >= $lockedBatch->capacity) abort(422, 'Batch is full.');

            $queueNumber = sprintf('B%02d-%03d', $lockedBatch->batch_number, $count + 1);

            $registration = Registration::create([
                'event_id'            => $event->id,
                'batch_id'            => $lockedBatch->id,
                'employee_identifier' => $employeeId,
                'employee_name'       => $employeeName,
                'queue_number'        => $queueNumber,
            ]);

            return ['type' => 'new', 'registration' => $registration];
        });

        if ($result['type'] === 'existing') {
            return redirect()
                ->route('employee.events.show', $event)
                ->with('already_registered', true)
                ->with('already_registered_message', 'NIP / Employee ID ini sudah terdaftar. Silakan check your ticket.');
        }

        return redirect()
            ->route('employee.ticket', ['registration' => $result['registration']->id])
            ->with('success', 'Register sukses. Ini tiket kamu.');
    }

    // Ticket display
   public function ticket(Registration $registration)
    {
        $registration->load(['event', 'batch']);

        // Ticket hangus kalau sudah check-in / batch lewat / event end
        if ($registration->isExpired()) {

            if ($registration->checked_in_at) {
                return redirect()
                    ->route('employee.events.index')
                    ->with('ticket_checked_in', true)
                    ->with('ticket_checked_in_message', 'Ticket kamu sudah digunakan (check-in). Jika ada kesalahan, hubungi admin.');
            }

            return redirect()
                ->route('employee.events.index')
                ->with('ticket_expired', true)
                ->with('ticket_expired_message', 'Ticket kamu sudah hangus karena batch/event sudah lewat.');
        }

        return view('employee.ticket', compact('registration'));
    }




    // Ticket login form and submit
    public function ticketLoginForm(Request $request)
    {
        $eventId = $request->query('event_id');
        return view('employee.ticket-login', compact('eventId'));
    }

    public function ticketLoginSubmit(Request $request)
    {
        $data = $request->validate([
            'employee_id'   => ['required', 'string', 'max:50'],
            'employee_name' => ['required', 'string', 'max:255'],
            'event_id'      => ['required', 'integer'], // ⛔ wajib
        ]);

        $registration = Registration::where('event_id', $data['event_id'])
            ->where('employee_identifier', $data['employee_id'])
            ->whereRaw('LOWER(employee_name) = LOWER(?)', [$data['employee_name']])
            ->first();

        if (!$registration) {
            return back()
                ->withInput()
                ->with('ticket_not_found', true)
                ->with('ticket_not_found_message',
                    'Kamu belum terdaftar di event ini. Silakan pilih batch dan register terlebih dahulu.'
                )
                ->with('ticket_not_found_event_id', $data['event_id']);
        }

        $registration->loadMissing(['event', 'batch']);

        if ($registration->isExpired()) {
            if ($registration->checked_in_at) {
                return redirect()
                    ->route('employee.ticket.loginForm', ['event_id' => $data['event_id']])
                    ->withInput($request->only(['employee_id', 'employee_name']))
                    ->with('ticket_checked_in', true)
                    ->with('ticket_checked_in_message', 'Ticket kamu sudah digunakan (check-in). Jika ada kesalahan, hubungi admin.');
            }

            return redirect()
                ->route('employee.ticket.loginForm', ['event_id' => $data['event_id']])
                ->withInput($request->only(['employee_id', 'employee_name']))
                ->with('ticket_expired', true)
                ->with('ticket_expired_message', 'Ticket kamu sudah hangus karena batch/event sudah lewat.');
        }

        return redirect()
            ->route('employee.ticket', ['registration' => $registration->id])
            ->with('success', 'Login sukses. Ini tiket kamu.');
    }

}
