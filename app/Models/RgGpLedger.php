<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgGpLedger extends Model
{
    protected $table = 'rg_gp_ledger';

    public $timestamps = false;

    protected $fillable = [
        'owner_id', 'amount', 'reason', 'ref_type', 'ref_id', 'status', 'meta_json', 'created_at',
    ];
}
