<?php

namespace Andileong\Framework\Core\Middleware;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;

class HandlePreflightRequest extends Chainable
{
    public function __construct(protected Application $app)
    {
        //
    }

    public function handle(Request|null $request)
    {
        $cors = $this->app->get('cors');
        return $cors->handleMiddlewareRequest($this);
    }

}