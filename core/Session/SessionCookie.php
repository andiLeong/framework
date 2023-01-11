<?php

namespace Andileong\Framework\Core\Session;

use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Session\Contracts\Session;
use Carbon\Carbon;

class SessionCookie
{
    public function __construct(
        protected Request $request,
        protected Session $session,
        protected array   $config
    )
    {
        //
    }

    /**
     * set session cookie
     */
    public function set()
    {
        if (!$this->exist()) {
            $this->request->setCookie(
                $this->getSessionName(),
                $this->session->generateId(),
                Carbon::now()->addMinutes($this->getSessionExpire())
            );
        }
    }

    /**
     * extend session cookie
     */
    public function extend()
    {
        if ($cookie = $this->get()) {
            $this->request->setCookie(
                name: $this->getSessionName(),
                value: $cookie,
                ttl: Carbon::now()->addMinutes($this->getSessionExpire())
            );
        }
    }

    /**
     * check if request has session cookie
     * @return bool
     */
    private function exist()
    {
        return !is_null($this->request->cookie($this->getSessionName()));
    }

    /**
     * get the session cookie
     * @param $default
     * @return array|mixed|null
     */
    protected function get($default = null)
    {
        if ($this->exist()) {
            return $this->request->cookie($this->getSessionName());
        }

        return $default;
    }

    /**
     * get the cookie name
     * @return mixed
     */
    private function getSessionName()
    {
        return $this->config['name'];
    }

    /**
     * get the cookies expire minute
     * @return mixed
     */
    public function getSessionExpire()
    {
        return $this->config['expire'];
    }
}