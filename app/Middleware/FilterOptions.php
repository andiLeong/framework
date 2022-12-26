<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;

class FilterOptions extends Chainable
{
    public function handle(Request|null $request)
    {

        return $this->next($request);
    }
}