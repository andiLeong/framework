<?php

namespace Andileong\Framework\Core\Response\Pipes;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Session\Contracts\Session;

class SaveSession extends Chainable
{
    public function __construct(protected Session $session)
    {
        //
    }

    public function handle($response)
    {
        $this->session->save();
        return $this->next($response);
    }
}
