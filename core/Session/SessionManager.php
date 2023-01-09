<?php

namespace Andileong\Framework\Core\Session;

use Andileong\Framework\Core\Support\Traits\HasMultipleDrivers;

class SessionManager
{
    use HasMultipleDrivers;

    public function __construct(
        protected array $config
    )
    {
        //
    }

    public function createFileDriver()
    {
        $instance = new FileSessionHandler($this->config['path'],$this->config['expire']);
        return new Store($instance);
    }

    public function getDefaultDriverName()
    {
        return $this->getConfig()['default'];
    }

    public function getConfig()
    {
       return $this->config;
    }
}