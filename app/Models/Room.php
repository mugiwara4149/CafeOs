<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'name',
        'canvas_width',
        'canvas_height',
        'sort_order',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(FloorElement::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(FloorElement::class)->where('element_type', 'table');
    }



}