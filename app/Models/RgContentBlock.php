<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgContentBlock extends Model
{
    protected $table = 'rg_content_blocks';

    protected $fillable = ['owner_type', 'owner_id', 'sort_order', 'block_type', 'payload_json'];

    public function getPayloadAttribute(): array
    {
        if (!$this->payload_json) return [];
        $decoded = json_decode($this->payload_json, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function forOwner(string $type, int $id)
    {
        return self::where('owner_type', $type)
            ->where('owner_id', $id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
