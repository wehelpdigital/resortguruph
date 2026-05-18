<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgGpTopup extends Model
{
    protected $table = 'rg_gp_topups';

    protected $fillable = [
        'owner_id', 'php_amount', 'gp_amount', 'gcash_ref_number', 'gcash_phone',
        'screenshot_path', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];
}
