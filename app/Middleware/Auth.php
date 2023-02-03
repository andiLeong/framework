<?php

namespace App\Middleware;

use Andileong\Framework\Core\Auth\Contracts\Guard as GuardContract;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Auth extends Chainable
{
    public function __construct(protected GuardContract $auth)
    {
        //
    }

    public function handle(Request|null $request)
    {
        if ($this->auth->check()) {
            return $this->next($request);
        }

        $response = new JsonResponse([
            'message' => 'you are not login',
            'code' => 403
        ], 403);
        $this->break($response);
    }
}
