<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RgSetting extends Model
{
    protected $table = 'rg_settings';

    protected $fillable = ['key', 'value', 'type', 'label', 'group', 'help_text', 'sort_order'];

    public static function get(string $key, $default = null)
    {
        $cached = Cache::remember('rg_settings_all', 300, function () {
            return self::all()->keyBy('key');
        });
        $row = $cached->get($key);
        if (!$row) return $default;
        return match ($row->type) {
            'int' => (int) $row->value,
            'bool' => (bool) $row->value,
            'json' => json_decode($row->value, true),
            default => $row->value,
        };
    }

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('rg_settings_all'));
        static::deleted(fn() => Cache::forget('rg_settings_all'));
    }
}
