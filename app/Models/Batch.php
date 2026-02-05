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
            '#F97316', // orange
            '#F59E0B', // amber
            '#EAB308', // yellow
            '#84CC16', // lime
            '#22C55E', // green
            '#16A34A', // green dark
            '#14B8A6', // teal
            '#06B6D4', // cyan
            '#0EA5E9', // sky
            '#3B82F6', // blue
            '#2563EB', // blue dark
            '#6366F1', // indigo
            '#4F46E5', // indigo dark
            '#8B5CF6', // violet
            '#A855F7', // purple
            '#D946EF', // fuchsia
            '#EC4899', // pink
            '#F43F5E', // rose
            '#BE123C', // rose dark
            '#7C2D12', // brown
            '#92400E', // brown amber
            '#374151', // slate
            '#111827', // near-black
            '#0F766E', // teal dark
        ];
    }

    public static function colorByIndex(int $index): string
    {
        $palette = self::colorPalette();
        return $palette[$index % count($palette)];
    }
}
