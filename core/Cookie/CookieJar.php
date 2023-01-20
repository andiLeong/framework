<?php

namespace Andileong\Framework\Core\Cookie;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CookieJar
{
    protected $cookies = [];

    /**
     * create a cookie instance
     * @param $name
     * @param $value
     * @param $ttl
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     *
     * @return Cookie
     */
    public function make($name, $value, $ttl, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        return new Cookie($name, $value, $ttl, $path, $domain, $secure, $httpOnly);
    }

    /**
     * push a cookie instance to the jar
     * @param ...$args
     */
    public function push(...$args)
    {
        if (isset($args[0]) && $args[0] instanceof Cookie) {
            $cookie = $args[0];
        } else {
            $cookie = $this->make(...$args);
        }

        $this->cookies[$cookie->getName()] = $cookie;
    }

    /**
     * get all the available cookies
     * @return array|mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * persist all the cookie to the Symfony response
     * @param Response $response
     */
    public function persist(Response $response)
    {
        foreach ($this->cookies as $cookie){
            $response->headers->setCookie($cookie);
        }
    }
}