<?php

use App\Models\User;

return [
    'default' => env('AUTH_DRIVER', 'token'),
    'drivers' => [
        'token' => [
            'provider' => [
                'model' => User::class,
                'column' => 'remember_token'
            ]
        ]
    ]
];