<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgBlogComment extends Model
{
    protected $table = 'rg_blog_comments';
    protected $guarded = ['id'];
    protected $casts = ['is_seeded' => 'boolean'];

    public function post()
    {
        return $this->belongsTo(RgBlogPost::class, 'blog_post_id');
    }

    public function avatarUrl(): string
    {
        if (!empty($this->commenter_avatar) && preg_match('#^https?://#i', $this->commenter_avatar)) {
            return $this->commenter_avatar;
        }
        return \App\Support\Avatar::initials($this->commenter_name);
    }
}
