<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Container\Container;

class Bootstrap
{
    protected $bootstrapers = [
        LoadConfiguration::class,
        SetEnvironmentVariable::class
    ];


    public function __construct(public Container $container)
    {
        //
    }

    public function boot()
    {
        foreach ($this->bootstrapers as $bootstraper) {
            $instance = $this->container->get($bootstraper);
            $instance->bootstrap($this->container);
        }
    }
}