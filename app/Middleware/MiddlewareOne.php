<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;

class MiddlewareOne extends Chainable
{
    public function handle($request)
    {
//        throw new Exception('smth wrong');
        $this->break(json(['msg' => 'break the pipeline'], 400));
        return $this->next($request);
    }
}