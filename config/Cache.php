<?php

return [
    'default' => env('CACHE_DRIVER', 'file'),
    'drivers' => [
        'redis' => [
            'prefix' => env('CACHE_REDIS_PREFIX','cache_')
        ]
    ]
];