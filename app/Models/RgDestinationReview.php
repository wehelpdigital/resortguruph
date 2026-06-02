<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgDestinationReview extends Model
{
    protected $table = 'rg_destination_reviews';
    protected $guarded = ['id'];
    protected $casts = ['review_date' => 'date', 'is_featured' => 'boolean'];

    public function keyword()
    {
        return $this->belongsTo(RgKeyword::class, 'keyword_id');
    }

    public function avatarUrl(): string
    {
        if (empty($this->reviewer_avatar)) {
            return 'https://api.dicebear.com/7.x/notionists/svg?seed=' . urlencode($this->reviewer_name);
        }
        if (preg_match('#^https?://#i', $this->reviewer_avatar)) {
            return $this->reviewer_avatar;
        }
        return asset('storage/' . ltrim($this->reviewer_avatar, '/'));
    }
}
