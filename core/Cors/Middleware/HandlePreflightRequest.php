<?php

namespace Andileong\Framework\Core\Cors\Middleware;

use Andileong\Framework\Core\Cors\Cors;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;

class HandlePreflightRequest extends Chainable
{
    public function __construct(protected Cors $cors)
    {
        //
    }

    public function handle(Request|null $request)
    {
        return $this->cors->handleMiddlewareRequest($this, $request);
    }
}
