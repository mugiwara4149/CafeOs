<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'element_type',
        'label',
        'shape',
        'x',
        'y',
        'width',
        'height',
        'rotation',
        'seats',
    ];

    protected function casts(): array
    {
        return [
            'x' => 'integer',
            'y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'rotation' => 'integer',
            'seats' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}