<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Session\SessionCookie;

class CreateCookies extends Chainable
{
    public function __construct(protected SessionCookie $sessionCookie)
    {
        //
    }

    public function handle(Request|null $request)
    {
        $this->sessionCookie->set();
        return $this->next($request);
    }

}