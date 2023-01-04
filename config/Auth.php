<?php

use Andileong\Framework\Core\tests\stubs\User as UserStub;
use App\Models\User;

return [
    'default' => env('AUTH_DRIVER', 'jwt'),


    'guards' => [
        'token' => [
            'driver' => 'token',
            'provider' => [
                'model' => User::class,
                'column' => 'remember_token'
            ],
        ],
        'admin' => [
            'driver' => 'token',
            'provider' => [
                'model' => UserStub::class,
                'column' => 'remember_token'
            ],
        ],
        'jwt' => [
            'driver' => 'jwt',
            'provider' => [
                'model' => User::class,
//                'column' => 'remember_token'
            ],
        ],
    ],

];