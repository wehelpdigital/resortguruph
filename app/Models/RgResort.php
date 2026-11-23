<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgResort extends Model
{
    protected $table = 'rg_resorts';

    protected $fillable = [
        'owner_id', 'name', 'slug', 'tagline', 'description_html',
        'region', 'province', 'city', 'address', 'lat', 'lng',
        'phone', 'email', 'website', 'fb', 'ig', 'tt',
        'price_range', 'capacity', 'amenities_json',
        'primary_color', 'secondary_color',
        'logo_path', 'hero_path', 'status', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function owner()
    {
        return $this->belongsTo(RgOwner::class, 'owner_id');
    }

    public function media()
    {
        return $this->hasMany(RgResortMedia::class, 'resort_id')->orderBy('sort_order');
    }

    /**
     * Real properties only: the seeded sample resorts ("Sample property for demo
     * purposes", @dummy.test emails) stay out of public listings, search and the sitemap.
     */
    public function scopeReal($query)
    {
        return $query->where(fn ($q) => $q->whereNull('email')->orWhere('email', 'not like', '%@dummy.test'))
            ->where(fn ($q) => $q->whereNull('description_html')->orWhere('description_html', 'not like', '%Sample property for demo purposes%'));
    }

    public function isDemo(): bool
    {
        return str_ends_with((string) $this->email, '@dummy.test') || str_contains((string) $this->description_html, 'Sample property for demo purposes');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getAmenitiesAttribute(): array
    {
        return $this->amenities_json ? (json_decode($this->amenities_json, true) ?: []) : [];
    }
}
