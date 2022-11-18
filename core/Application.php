<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;

class Application extends Container
{

    public function __construct(protected $appPath = null)
    {
        $this->registerBinding();
        $this->boot();

    }

    public function registerBinding()
    {
        self::$instance = $this;

        $this->singleton('app_path', $this->appPath);
        $this->singleton(Request::class, fn() => new Request());
        $this->singleton(Router::class, fn() => new Router());
    }

    public function boot()
    {
        (new Bootstrap($this))->boot();
    }
}