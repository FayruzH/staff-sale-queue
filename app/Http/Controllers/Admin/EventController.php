<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Batch;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('id')->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'event_date' => ['required','date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'break_start' => ['nullable'],
            'break_end' => ['nullable'],
            'batch_duration_min' => ['required','integer','min:1','max:180'],
            'gap_min' => ['required','integer','min:0','max:60'],
            'capacity_per_batch' => ['required','integer','min:1','max:500'],
        ]);

        // code "autonumber-ish" for MVP
        $data['code'] = 'EVT-' . Carbon::now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));
        $data['status'] = 'draft';

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
            'code' => 'required|string|max:255',
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
