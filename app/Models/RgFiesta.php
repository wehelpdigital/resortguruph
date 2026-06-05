<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgFiesta extends Model
{
    protected $table = 'rg_fiestas';

    protected $fillable = [
        'slug', 'name', 'region_cluster', 'province', 'city_or_town',
        'month', 'date_label', 'summary',
        'cover_image_path', 'og_image_path',
        'meta_title', 'meta_description', 'h1',
        'is_published', 'published_at', 'author_id', 'pageviews_30d',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'month' => 'integer',
        'pageviews_30d' => 'integer',
    ];

    /** Region label lookup for the public list page. */
    public const REGION_LABELS = [
        'north-luzon' => 'North Luzon',
        'central-luzon' => 'Central Luzon',
        'metro-manila' => 'Metro Manila',
        'south-luzon' => 'South Luzon (CALABARZON)',
        'bicol' => 'Bicol',
        'visayas' => 'Visayas',
        'mindanao' => 'Mindanao',
        'palawan' => 'Palawan + MIMAROPA',
        'marinduque' => 'Marinduque',
    ];

    public const MONTH_LABELS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    public function coverUrl(): string
    {
        $path = $this->cover_image_path;
        if (!$path) return '';
        if (str_starts_with($path, 'http')) return $path;
        if (str_starts_with($path, '/storage/')) return $path;
        return '/storage/' . ltrim($path, '/');
    }

    public function regionLabel(): string
    {
        return self::REGION_LABELS[$this->region_cluster] ?? ucwords(str_replace('-', ' ', (string) $this->region_cluster));
    }

    public function monthLabel(): string
    {
        return $this->month ? (self::MONTH_LABELS[$this->month] ?? '') : '';
    }
}
