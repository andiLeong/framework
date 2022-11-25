<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use App\Exception\Handler;

class Application extends Container
{
    protected $aliasMapping = [
        'app' => [Application::class],
        'request' => [Request::class],
        'router' => [Router::class],
        'config' => [Config::class],
        'db' => [Connection::class],
        'exception.handler' => [Handler::class],
    ];

    private $inProduction = false;

    public function __construct(protected $appPath = null, protected $request = null)
    {
        $this->loadAlias();
        $this->registerBinding();
        $this->boot();
        $this->setInProduction();
    }

    public function registerBinding()
    {
        self::$instance = $this;

        $this->bind('app_path', $this->appPath);
        $this->singleton($this->getAlias(Request::class), fn() => $this->request ?? new Request());
        $this->singleton($this->getAlias(Router::class), fn($app) => new Router($app));
        $this->singleton($this->getAlias(Connection::class), fn() => new Connection());
        $this->bind($this->getAlias(Handler::class), fn($app, $args) => new Handler($app,$args[0]));
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

    /**
     * @return bool
     */
    public function isInProduction(): bool
    {
        return $this->inProduction;
    }

    /**
     * set if we are in production env
     */
    public function setInProduction(): void
    {
        $this->inProduction = env('APP_DEBUG') == 'false';
    }
}