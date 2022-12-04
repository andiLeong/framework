<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;

class MiddlewareTwo extends Chainable
{
    public function handle($request)
    {
        return $this->next($request);
    }

}