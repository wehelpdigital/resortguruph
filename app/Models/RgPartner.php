<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgPartner extends Model
{
    protected $table = 'rg_partners';

    protected $fillable = [
        'owner_id', 'name', 'slug', 'type', 'city', 'region',
        'tagline', 'description', 'image_path', 'rating', 'review_count',
        'is_verified', 'is_featured', 'phone', 'website', 'status',
    ];

    protected $casts = [
        'rating' => 'float',
        'review_count' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(RgOwner::class, 'owner_id');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    /**
     * Display metadata (label, palette, icon) for each partner type,
     * shared by the controller and the directory view so the card
     * colours, filter chips, and type labels stay in sync.
     */
    public static function typeMeta(): array
    {
        return [
            'hotel'        => ['label' => 'Hotel',            'color' => 'brand',   'icon' => 'M3 21V7l9-4 9 4v14 M9 21v-5h6v5 M8 9h.01 M12 9h.01 M16 9h.01 M8 13h.01 M12 13h.01 M16 13h.01'],
            'resort'       => ['label' => 'Resort',           'color' => 'emerald', 'icon' => 'M12 2v6 M12 8c-3 0-7 1.5-9 4h18c-2-2.5-6-4-9-4z M3 12v9h18v-9 M9 21v-4h6v4'],
            'homestay'     => ['label' => 'Homestay',         'color' => 'amber',   'icon' => 'M3 11l9-7 9 7 M5 10v10h14V10 M10 20v-6h4v6'],
            'restaurant'   => ['label' => 'Restaurant',       'color' => 'rose',    'icon' => 'M4 3v7a3 3 0 0 0 6 0V3 M7 3v18 M17 3c-1.5 0-3 1.8-3 4.5S15.5 12 17 12s3 3 3 3v6'],
            'cafe'         => ['label' => 'Cafe',             'color' => 'orange',  'icon' => 'M4 8h13a1 1 0 0 1 1 1v2a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8z M18 9h1a2 2 0 0 1 0 4h-1 M6 2v2 M10 2v2 M14 2v2 M4 20h14'],
            'massage_spa'  => ['label' => 'Massage & Spa',    'color' => 'violet',  'icon' => 'M12 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6z M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6'],
            'surf_school'  => ['label' => 'Surf School',      'color' => 'sky',     'icon' => 'M2 18c2 0 2-1.5 4-1.5S8 18 10 18s2-1.5 4-1.5 2 1.5 4 1.5 M6 15c4-8 9-9 14-8-1 5-4 9-11 9'],
            'dive_school'  => ['label' => 'Dive School',      'color' => 'cyan',    'icon' => 'M12 2a3 3 0 0 1 3 3v5 M9 9a3 3 0 0 0 6 0 M6 13c0 5 3 8 6 8s6-3 6-8 M4 13h16'],
            'tour_guide'   => ['label' => 'Tour Guide',       'color' => 'indigo',  'icon' => 'M12 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8z M4 22a8 8 0 0 1 16 0 M18 8l3-1-1 3'],
            'travel_tour'  => ['label' => 'Travel & Tours',   'color' => 'teal',    'icon' => 'M2 12h20 M12 2a15 15 0 0 1 0 20 M12 2a15 15 0 0 0 0 20 M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z'],
            'transport'    => ['label' => 'Transport & Rental','color' => 'slate',  'icon' => 'M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11v6H5v-6z M5 11h14 M7.5 14h.01 M16.5 14h.01'],
            'other'        => ['label' => 'Tourism Service',  'color' => 'slate',   'icon' => 'M12 2l2.4 6.9H22l-6 4.3 2.3 7L12 16.9 5.7 20.2 8 13.2 2 8.9h7.6z'],
        ];
    }
}
