<?php

namespace Andileong\Framework\Core\Config;

use Andileong\Framework\Core\Support\Arr;

class Config implements \ArrayAccess
{
    protected $configs = [];

    public function set($key, $value)
    {
        $this->configs[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->configs, $key, $default);
    }

    public function offsetExists(mixed $offset) :bool
    {
        return isset($this->configs[$offset]);
    }

    public function offsetGet(mixed $offset) :mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value) :void
    {
        $this->configs[$offset] = $value;
    }

    public function offsetUnset(mixed $offset) :void
    {
        unset($this->configs[$offset]);
    }
}