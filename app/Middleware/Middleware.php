<?php

namespace App\Middleware;

class Middleware
{
    public $middlewares = [
        'one' => MiddlewareOne::class,
        'two' => MiddlewareTwo::class,
        'auth' => Auth::class,
    ];
}