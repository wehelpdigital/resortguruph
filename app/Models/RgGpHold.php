<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgGpHold extends Model
{
    protected $table = 'rg_gp_holds';

    public $timestamps = false;

    protected $fillable = [
        'owner_id', 'amount', 'reason', 'ref_type', 'ref_id', 'status',
        'expires_at', 'released_at', 'created_at',
    ];
}
