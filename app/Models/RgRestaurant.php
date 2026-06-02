<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RgRestaurant extends Model
{
    protected $table = 'rg_restaurants';

    protected $fillable = [
        'owner_id', 'name', 'slug', 'tagline', 'description_html',
        'cuisine', 'price_range', 'region', 'province', 'city', 'address',
        'lat', 'lng', 'phone', 'email', 'website', 'fb', 'ig',
        'hours_summary', 'primary_color', 'secondary_color',
        'logo_path', 'hero_path', 'status', 'approved_at',
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
        return $this->hasMany(RgRestaurantMedia::class, 'restaurant_id')->orderBy('sort_order');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(RgRestaurantListing::class, 'restaurant_id');
    }
}
