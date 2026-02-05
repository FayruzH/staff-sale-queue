<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function start(Batch $batch)
    {
        DB::transaction(function () use ($batch) {
            // lock event's batches biar konsisten
            $eventId = $batch->event_id;

            // complete semua batch yang lagi running (biar cuma 1 yang running)
            Batch::where('event_id', $eventId)
                ->where('status', 'running')
                ->update(['status' => 'completed']);

            // start batch yang dipilih
            $batch->refresh();
            $batch->update([
                'status' => 'running',
                'started_at' => now(),
            ]);
        });

        return back()->with('success', 'Batch started!');
    }

    // optional buat demo manual complete
    public function complete(Batch $batch)
    {
        $batch->update(['status' => 'completed']);
        return back()->with('success', 'Batch completed!');
    }
}
