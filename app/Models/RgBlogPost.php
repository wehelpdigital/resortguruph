<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgBlogPost extends Model
{
    protected $table = 'rg_blog_posts';

    protected $fillable = [
        'author_id', 'title', 'subtitle', 'slug', 'excerpt', 'tldr', 'wwww_json',
        'content_html', 'cover_path', 'meta_title', 'meta_description',
        'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'wwww_json'    => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
