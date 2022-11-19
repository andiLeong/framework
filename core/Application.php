<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;

class Application extends Container
{
    protected $aliasMapping = [
        'app' => [Application::class],
        'request' => [Request::class],
        'router' => [Router::class],
        'config' => [Config::class],
    ];

    public function __construct(protected $appPath = null)
    {
        $this->loadAlias();
        $this->registerBinding();
        $this->boot();

    }

    public function registerBinding()
    {
        self::$instance = $this;

        $this->bind('app_path', $this->appPath);
        $this->singleton($this->getAlias(Request::class), fn() => new Request());
        $this->singleton($this->getAlias(Router::class), fn() => new Router());
    }

    public function boot()
    {
        (new Bootstrap($this))->boot();
    }

    private function loadAlias()
    {
        foreach($this->aliasMapping as $key => $alias){
           foreach ($alias as $alia){
              $this->alias[$alia] = $key;
           }
        }
    }
}