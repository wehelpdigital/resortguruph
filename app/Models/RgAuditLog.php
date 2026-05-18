<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgAuditLog extends Model
{
    protected $table = 'rg_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'actor_type', 'actor_id', 'action', 'target_type', 'target_id',
        'meta_json', 'ip', 'user_agent', 'created_at',
    ];

    public static function record(string $action, ?array $extra = null): void
    {
        $owner = auth()->user();
        self::create([
            'actor_type' => $owner ? 'owner' : 'system',
            'actor_id' => $owner?->id,
            'action' => $action,
            'target_type' => $extra['target_type'] ?? null,
            'target_id' => $extra['target_id'] ?? null,
            'meta_json' => isset($extra['meta']) ? json_encode($extra['meta']) : null,
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
            'created_at' => now(),
        ]);
    }
}
