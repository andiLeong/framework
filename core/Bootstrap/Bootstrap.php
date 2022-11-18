<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Application;

class Bootstrap
{
    protected $bootstrapers = [
        LoadConfiguration::class,
        SetEnvironmentVariable::class
    ];


    public function __construct(public Application $app)
    {
        //
    }

    public function boot()
    {
        foreach ($this->bootstrapers as $bootstraper) {
            $instance = $this->app->get($bootstraper);
            $instance->bootstrap($this->app);
        }
    }
}