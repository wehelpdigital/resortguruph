<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RgAdventureMedia extends Model
{
    protected $table = 'rg_adventure_media';
    protected $fillable = ['adventure_id', 'kind', 'path', 'caption', 'sort_order'];

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(RgAdventure::class, 'adventure_id');
    }
}
