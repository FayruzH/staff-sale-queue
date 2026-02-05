<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Registration extends Model
{
    protected $fillable = [
        'event_id',
        'batch_id',
        'employee_identifier',
        'employee_name',
        'queue_number',
        'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function isExpired(): bool
    {
        $this->loadMissing(['event', 'batch']);

        // 1) udah check-in
        if (!is_null($this->checked_in_at)) {
            return true;
        }

        // safety
        if (!$this->event || !$this->batch) {
            return true;
        }

        // 2) event tidak aktif
        if ($this->event->status !== 'active') {
            return true;
        }

        $now = now();

        // 3) event end lewat (event_date + end_time)
        if ($this->event->event_date && $this->event->end_time) {
            $eventEnd = Carbon::parse($this->event->event_date . ' ' . $this->event->end_time);
            if ($now->greaterThan($eventEnd)) {
                return true;
            }
        }

        // 4) batch status completed
        if ($this->batch->status === 'completed') {
            return true;
        }

        // 5) batch end lewat (event_date + batch end_time)
        if ($this->event->event_date && $this->batch->end_time) {
            $batchEnd = Carbon::parse($this->event->event_date . ' ' . $this->batch->end_time);
            if ($now->greaterThan($batchEnd)) {
                return true;
            }
        }

        return false;
    }
}
