<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgKeyword extends Model
{
    protected $table = 'rg_keywords';

    protected $fillable = [
        'phrase', 'slug', 'search_volume_monthly', 'keyword_difficulty',
        'cluster_tag', 'intent', 'notes', 'status', 'listing_capacity_top', 'base_price_gp',
    ];

    public function seoPages()
    {
        return $this->hasMany(RgSeoPage::class, 'keyword_id');
    }

    // Backward-compat: primary page (or first page if no primary marked)
    public function seoPage()
    {
        return $this->hasOne(RgSeoPage::class, 'keyword_id')->orderByDesc('is_primary')->orderBy('id');
    }

    public function primaryPage()
    {
        return $this->hasOne(RgSeoPage::class, 'keyword_id')->where('is_primary', true);
    }

    public function listings()
    {
        return $this->hasMany(RgListing::class, 'keyword_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
