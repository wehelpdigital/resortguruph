<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RgAdventure extends Model
{
    protected $table = 'rg_adventures';

    protected $fillable = [
        'owner_id', 'name', 'slug', 'tagline', 'description_html',
        'activity_type', 'difficulty', 'duration_minutes', 'min_age', 'max_group',
        'price_range', 'includes',
        'region', 'province', 'city', 'address', 'lat', 'lng',
        'phone', 'email', 'website', 'fb', 'ig',
        'primary_color', 'secondary_color', 'logo_path', 'hero_path',
        'status', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(RgOwner::class, 'owner_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(RgAdventureMedia::class, 'adventure_id')->orderBy('sort_order');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(RgAdventureListing::class, 'adventure_id');
    }
}
