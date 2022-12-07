<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Auth extends Chainable
{
    public function handle(Request|null $request)
    {
        if (!$request->exist('foo')) {
            $response = new JsonResponse(['msg' => 'you are not login'], 403);
            $this->break($response);
        }

        return $this->next($request);
    }
}