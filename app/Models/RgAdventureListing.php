<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgAdventureListing extends Model
{
    protected $table = 'rg_adventure_listings';

    protected $fillable = [
        'keyword_id', 'adventure_id', 'owner_id',
        'base_gp', 'bid_gp', 'starts_at', 'expires_at', 'last_bid_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_bid_at' => 'datetime',
    ];

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(RgKeyword::class, 'keyword_id');
    }

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(RgAdventure::class, 'adventure_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(RgOwner::class, 'owner_id');
    }
}
