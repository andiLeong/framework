<?php

namespace App\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Support\Str;
use Carbon\Carbon;

class CreateCookies extends Chainable
{
    public function __construct(protected array $sessionConfig)
    {
        //
    }

    public function handle(Request|null $request)
    {
        if ($request->cookie($this->getSessionName()) === null) {

            $request->setCookie(
                $this->getSessionName(),
                Str::random(32),
                Carbon::now()->addDay()
            );

        }

        return $this->next($request);
    }

    protected function getSessionName()
    {
        return $this->sessionConfig['name'];
    }
}