<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RgOwner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'rg_owners';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'status',
        'last_login_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function resorts()
    {
        return $this->hasMany(RgResort::class, 'owner_id');
    }

    public function listings()
    {
        return $this->hasMany(RgListing::class, 'owner_id');
    }

    public function getGoldPointsBalanceAttribute(): int
    {
        $posted = (int) RgGpLedger::where('owner_id', $this->id)
            ->where('status', 'posted')
            ->sum('amount');
        $held = (int) RgGpHold::where('owner_id', $this->id)
            ->where('status', 'active')
            ->sum('amount');
        return $posted - $held;
    }
}
