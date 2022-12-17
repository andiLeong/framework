<?php

namespace App\Middleware;

use Andileong\Framework\Core\Auth\Auth as AuthContract;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Auth extends Chainable
{
    public function __construct(protected AuthContract $auth)
    {
       //
    }

    public function handle(Request|null $request)
    {
        if ($this->auth->check()) {
            return $this->next($request);
        }

        $response = new JsonResponse(['msg' => 'you are not login'], 403);
        $this->break($response);
    }
}