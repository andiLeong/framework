<?php

namespace App\Middleware;

use Andileong\Framework\Core\Cors\Middleware\HandlePreflightRequest;
use Andileong\Framework\Core\Session\Middleware\StartSession;

class Middleware
{
    public $golbalMiddlewares = [
        HandlePreflightRequest::class,
        StartSession::class,
    ];

    public $middlewares = [
        'one' => MiddlewareOne::class,
        'two' => MiddlewareTwo::class,
        'auth' => Auth::class,
    ];
}