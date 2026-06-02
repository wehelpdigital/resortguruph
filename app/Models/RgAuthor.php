<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgAuthor extends Model
{
    protected $table = 'rg_authors';
    protected $guarded = ['id'];

    public function avatarUrl(): string
    {
        if (empty($this->avatar_path)) {
            return 'https://api.dicebear.com/7.x/notionists/svg?seed=' . urlencode($this->name);
        }
        if (preg_match('#^https?://#i', $this->avatar_path)) {
            return $this->avatar_path;
        }
        return asset('storage/' . ltrim($this->avatar_path, '/'));
    }

    public function pages()
    {
        return $this->hasMany(RgSeoPage::class, 'author_id');
    }
}
