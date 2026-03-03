<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // (opsional) biar bisa mass-assign saat create event
    protected $fillable = [
        'code',
        'name',
        'location',
        'description',
        'thumbnail',
        'event_date',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'batch_duration_min',
        'gap_min',
        'capacity_per_batch',
        'status',
        'is_auto_mode',
    ];

    protected $casts = [
        'is_auto_mode' => 'boolean',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
