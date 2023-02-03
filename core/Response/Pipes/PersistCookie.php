<?php

namespace Andileong\Framework\Core\Response\Pipes;

use Andileong\Framework\Core\Cookie\CookieJar;
use Andileong\Framework\Core\Pipeline\Chainable;

class PersistCookie extends Chainable
{
    public function __construct(protected CookieJar $cookieJar)
    {
        //
    }

    public function handle($response)
    {
        $this->cookieJar->persist($response);
        return $this->next($response);
    }
}
