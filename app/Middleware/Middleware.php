<?php

namespace App\Middleware;

use Andileong\Framework\Core\Middleware\HandlePreflightRequest;
use Andileong\Framework\Core\Session\Middleware\StartSession;

class Middleware
{
    public $golbalMiddlewares = [
        StartSession::class,
        HandlePreflightRequest::class,
    ];

    public $middlewares = [
        'one' => MiddlewareOne::class,
        'two' => MiddlewareTwo::class,
        'auth' => Auth::class,
    ];
}