<?php

namespace Andileong\Framework\Core\Response\Pipes;

use Andileong\Framework\Core\Cors\Cors;
use Andileong\Framework\Core\Pipeline\Chainable;

class AddCors extends Chainable
{
    public function __construct(protected Cors $cors)
    {

    }

    public function handle($response)
    {
        $this->cors->handleResponse($response);
        return $this->next($response);
    }
}