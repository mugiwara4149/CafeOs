<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'logo_path',
        'banner_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order');
    }

   public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('category')->orderBy('name');
    }

    public function staffMembers(): HasMany
    {
        return $this->hasMany(StaffMember::class)->orderBy('role')->orderBy('name');
    }

    
}