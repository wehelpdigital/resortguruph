<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgBlogPost extends Model
{
    protected $table = 'rg_blog_posts';

    protected $fillable = [
        'author_id', 'title', 'slug', 'excerpt', 'content_html',
        'cover_path', 'meta_title', 'meta_description', 'status', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
