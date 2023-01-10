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
        $session = $this->manager->driver();
        $id = $request->cookie($this->config()['name'],$session->generateId());

        $session->setId($id);
        $session->start();

        $this->clean($session);

        $this->next($request);
    }

    protected function clean(Session $session)
    {
        $session->clean($this->config()['expire'] * 60);
    }

    protected function config()
    {
        return $this->manager->getConfig();
    }
}