<?php

namespace Andileong\Framework\Core\Config;

use Andileong\Framework\Core\Support\Arr;

class Config
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
}