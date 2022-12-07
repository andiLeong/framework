<?php

namespace App\Middleware;

class Middleware
{
    public static $middlewares = [
        'one' => MiddlewareOne::class,
        'two' => MiddlewareTwo::class,
        'auth' => Auth::class,
    ];
}