<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableCall extends Model
{
    protected $fillable = [
        'cafe_id',
        'floor_element_id',
        'request_type',
        'note',
        'status',
        'resolved_by_staff_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
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

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'resolved_by_staff_id');
    }
}
