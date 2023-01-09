<?php

namespace Andileong\Framework\Core\Session\Contracts;

interface Session
{

    /**
     * set a key/value as session
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * get a session value from session
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * delete a session key
     * @param $key
     * @return mixed
     */
    public function delete($key);

    /**
     * remove all session keys
     * @return mixed
     */
    public function remove();

    /**
     * get all the session
     * @return mixed
     */
    public function all();

    /**
     * Checks if a key exists.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key);

    /**
     * Checks if a key is present and not null.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key);
}