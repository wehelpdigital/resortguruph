<?php

use App\Models\RgOwner;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'owners'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'owners',
        ],
        // Alias used by member-only comment + review forms. Mirrors web so
        // both @auth('owner') and @auth resolve to the same logged-in owner.
        'owner' => [
            'driver' => 'session',
            'provider' => 'owners',
        ],
    ],

    'providers' => [
        'owners' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', RgOwner::class),
        ],
    ],

    'passwords' => [
        'owners' => [
            'provider' => 'owners',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'rg_password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
