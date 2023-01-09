<?php

namespace Andileong\Framework\Core\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Support\Cors;

class HandlePreflightRequest extends Chainable
{
    public function __construct(protected Cors $cors)
    {
        //
    }

    public function handle(Request|null $request)
    {
        return $this->cors->handleMiddlewareRequest($this);
    }

}