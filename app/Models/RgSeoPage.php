<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgSeoPage extends Model
{
    protected $table = 'rg_seo_pages';

    protected $fillable = [
        'keyword_id', 'slug', 'title', 'meta_title', 'meta_description', 'meta_keywords',
        'canonical_url', 'robots', 'og_image_path', 'h1', 'intro_html', 'body_html',
        'faq_json', 'schema_json', 'fallback_listing_html', 'is_published', 'is_primary',
        'published_at', 'pageviews_30d', 'pageviews_total',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_primary' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function keyword()
    {
        return $this->belongsTo(RgKeyword::class, 'keyword_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
