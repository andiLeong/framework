<?php

namespace Andileong\Framework\Core\Session\Middleware;

use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Session\Contracts\Session;
use Andileong\Framework\Core\Session\SessionManager;

class StartSession extends Chainable
{

    public function __construct(protected SessionManager $manager)
    {
        //
    }

    public function handle(?Request $request)
    {
        $id = 'vGBTEMbxXZKreDdu0fgVOh9SYnso1PapzyWtACF7';
        $session = $this->manager->driver();
        $session->setId($id);
        $session->start();

        $this->clean($session);

        $this->next($request);
    }

    protected function clean(Session $session)
    {
        $session->clean($this->manager->getConfig()['expire'] * 60);
    }
}