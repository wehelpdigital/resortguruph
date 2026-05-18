<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgListing extends Model
{
    protected $table = 'rg_listings';

    protected $fillable = [
        'keyword_id', 'resort_id', 'owner_id', 'base_gp', 'bid_gp',
        'starts_at', 'expires_at', 'last_bid_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_bid_at' => 'datetime',
    ];

    public function keyword()
    {
        return $this->belongsTo(RgKeyword::class, 'keyword_id');
    }

    public function resort()
    {
        return $this->belongsTo(RgResort::class, 'resort_id');
    }

    public function owner()
    {
        return $this->belongsTo(RgOwner::class, 'owner_id');
    }
}
