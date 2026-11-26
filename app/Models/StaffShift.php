<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffShift extends Model
{
    protected $fillable = [
        'cafe_id',
        'staff_id',
        'clock_in_at',
        'clock_out_at',
        'orders_served',
        'calls_resolved',
    ];

    protected function casts(): array
    {
        return [
            'clock_in_at'  => 'datetime',
            'clock_out_at' => 'datetime',
        ];
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function getDurationFormattedAttribute(): string
    {
        $end =$this->clock_out_at ?? now();
        $minutes = (int) $this->clock_in_at->diffInMinutes($end);
        $hours = intdiv($minutes, 60);
        $mins =$minutes % 60;

        return "{$hours}h {$mins}m";
    }
}
