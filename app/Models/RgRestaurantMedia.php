<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgRestaurantMedia extends Model
{
    protected $table = 'rg_restaurant_media';
    protected $fillable = ['restaurant_id', 'kind', 'path', 'caption', 'sort_order'];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(RgRestaurant::class, 'restaurant_id');
    }
}
