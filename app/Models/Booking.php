<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'floor_element_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'booking_date',
        'time_slot',
        'guest_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'guest_count' => 'integer',
        ];
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(FloorElement::class, 'floor_element_id');
    }
}