<?php

return [
    'name' => 'framework',


    'timezone' => 'Asia/Hong_Kong',


    'providers' => [
        \Andileong\Framework\Core\Providers\CacheServiceProvider::class,
        \Andileong\Framework\Core\Providers\DatabaseServiceProvider::class,
        \Andileong\Framework\Core\Providers\AuthServiceProvider::class,
        \Andileong\Framework\Core\Providers\HashServiceProvider::class,
        \Andileong\Framework\Core\Providers\LogServiceProvider::class,
        \Andileong\Framework\Core\Providers\JwtServiceProvider::class,
        \Andileong\Framework\Core\Providers\CorsServiceProvider::class,
        \Andileong\Framework\Core\Providers\SessionServiceProvider::class,
        \Andileong\Framework\Core\Providers\ConsoleServiceProvider::class,
        \App\Provider\AppServiceProvider::class,
    ]
];
