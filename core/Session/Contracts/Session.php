<?php

namespace Andileong\Framework\Core\Session\Contracts;

interface Session
{

    public function set($key, $value);

    public function get($key, $default = null);

    public function delete($key);

    /**
     * remove all session keys
     * @return mixed
     */
    public function remove();

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