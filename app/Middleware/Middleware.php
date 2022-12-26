<?php

namespace App\Middleware;

use Andileong\Framework\Core\Middleware\HandlePreflightRequest;

class Middleware
{
    public $golbalMiddlewares = [
        HandlePreflightRequest::class,
    ];

    public $middlewares = [
        'one' => MiddlewareOne::class,
        'two' => MiddlewareTwo::class,
        'auth' => Auth::class,
    ];
}