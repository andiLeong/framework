<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class MiddlewareOne extends Chainable
{
    public function handle($request)
    {
//        throw new Exception('smth wrong');
//        $response = new JsonResponse(['msg' => 'break the pipeline'],400);
//        $this->break($response);
        return $this->next($request);
    }
}