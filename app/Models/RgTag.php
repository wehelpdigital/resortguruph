<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgTag extends Model
{
    protected $table = 'rg_tags';

    protected $fillable = [
        'tag', 'slug', 'keyword_id', 'position', 'search_volume_monthly', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
        'search_volume_monthly' => 'integer',
    ];

    public function keyword()
    {
        return $this->belongsTo(RgKeyword::class, 'keyword_id');
    }
}
