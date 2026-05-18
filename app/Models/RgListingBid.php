<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgListingBid extends Model
{
    protected $table = 'rg_listing_bids';

    public $timestamps = false;

    protected $fillable = [
        'listing_id', 'owner_id', 'keyword_id', 'action',
        'gp_amount', 'bid_gp_after', 'rank_after', 'days_added',
        'meta_json', 'created_at',
    ];

    public function listing()
    {
        return $this->belongsTo(RgListing::class, 'listing_id');
    }
}
