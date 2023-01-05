<?php

return [
    'name' => 'framework',


    'timezone' => 'Asia/Hong_Kong',


    'providers' => [
        \Andileong\Framework\Core\Providers\AuthServiceProvider::class,
        \Andileong\Framework\Core\Providers\JwtServiceProvider::class
    ]
];
