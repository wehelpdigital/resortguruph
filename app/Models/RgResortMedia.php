<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgResortMedia extends Model
{
    protected $table = 'rg_resort_media';

    protected $fillable = ['resort_id', 'kind', 'path', 'caption', 'sort_order'];

    public function resort()
    {
        return $this->belongsTo(RgResort::class, 'resort_id');
    }
}
