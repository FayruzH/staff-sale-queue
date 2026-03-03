<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $casts = [
        'started_at' => 'datetime',
    ];


    protected $fillable = [
        'event_id',
        'batch_number',
        'start_time',
        'end_time',
        'capacity',
        'color_code',
        'status',
        'started_at',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // helper: hitung jumlah yang sudah daftar
    public function getRegisteredCountAttribute()
    {
        return $this->registrations()->count();
    }

    // helper: sisa slot
    public function getRemainingSlotsAttribute()
    {
        return max(0, $this->capacity - $this->registered_count);
    }

    public static function colorPalette(): array
    {
        return [
            '#EF4444', // red
            '#F59E0B', // yellow
            '#22C55E', // green
            '#3B82F6', // blue
            '#A855F7', // purple
            '#F97316', // orange
            '#06B6D4', // cyan
            '#EC4899', // pink
        ];
    }

    public static function colorByIndex(int $index): string
    {
        $palette = self::colorPalette();
        return $palette[$index % count($palette)];
    }
}
