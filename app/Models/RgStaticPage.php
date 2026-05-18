<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgStaticPage extends Model
{
    protected $table = 'rg_static_pages';

    protected $fillable = ['slug', 'title', 'meta_title', 'meta_description', 'content_html', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
