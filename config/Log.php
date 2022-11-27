<?php

return [

    'default' => env('LOG_DRIVER','single'),

    'driver' => [
        'single' => [
            'path' => storagePath() . '/logs/system.log',
        ],
        'daily' => [
            'path' => storagePath() . '/logs/daily.log',
            'days' => 7,
        ]
    ]
];
