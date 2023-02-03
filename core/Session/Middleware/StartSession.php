<?php

namespace Andileong\Framework\Core\Session\Middleware;

use Andileong\Framework\Core\Cookie\CookieJar;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Session\Contracts\Session;
use Andileong\Framework\Core\Session\SessionManager;
use Carbon\Carbon;

class StartSession extends Chainable
{
    public function __construct(protected SessionManager $manager, protected CookieJar $cookieJar)
    {
        //
    }

    public function handle(?Request $request)
    {
        $session = $this->manager->driver();
        $id = $this->getSessionId($request, $session);

        $this->createCookie($id);
        $session->setId($id);
        $session->start();

        $this->clean($session);

        $this->next($request);
    }

    /**
     * clean the old session data
     * @param Session $session
     */
    protected function clean(Session $session)
    {
        $session->clean($this->config()['expire'] * 60);
    }

    /**
     * get the session config array
     * @return array
     */
    protected function config()
    {
        return $this->manager->getConfig();
    }

    /**
     * put the session cookie to the jar
     * @param $value
     */
    private function createCookie($value)
    {
        $this->cookieJar->push(
            $this->config()['name'],
            $value,
            Carbon::now()->addMinutes($this->config()['expire'])
        );
    }

    /**
     * @param Request|null $request
     * @param mixed $session
     * @return array|mixed
     */
    protected function getSessionId(?Request $request, mixed $session): mixed
    {
        if ($id = $request->cookie($this->config()['name'])) {
            return $id;
        }
        return $session->generateId();
    }
}
