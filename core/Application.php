<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Cache\CacheHandler;
use Andileong\Framework\Core\Cache\CacheManager;
use Andileong\Framework\Core\Cache\Contract\Cache;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Logs\LoggerManager;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use App\Console\Console;
use App\Exception\Handler;
use ErrorException;

class Application extends Container
{
    protected $aliasMapping = [
        'app' => [Application::class],
        'request' => [Request::class],
        'router' => [Router::class],
        'config' => [Config::class],
        'db' => [Connection::class],
        'exception.handler' => [Handler::class],
        'logger' => [LoggerManager::class],
        'console' => [Console::class],
        'cache' => [CacheManager::class],
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
        $this->bind('storage_path', $this->appPath . '/storage');
        $this->bind('stubs_path', $this->appPath . '/core/Stubs');
        $this->bind('public_path', $this->appPath . '/public');
        $this->singleton($this->getAlias(Request::class), fn() => $this->request ?? new Request());
        $this->singleton($this->getAlias(Router::class), fn($app) => new Router($app));
        $this->singleton($this->getAlias(Connection::class), fn() => new Connection());
        $this->singleton($this->getAlias(LoggerManager::class), fn($app) => new LoggerManager($app));
        $this->singleton($this->getAlias(Console::class), fn($app) => new Console($app));
        $this->singleton($this->getAlias(CacheManager::class), fn($app) => new CacheManager($app));
        $this->singleton(Cache::class, fn($app) => $app['cache']->driver());
        $this->singleton(CacheHandler::class, fn($app) => $app['cache']->driver());
        $this->bind($this->getAlias(Handler::class), fn($app, $args) => new Handler($app, $args[0]));
    }

    public function boot()
    {
        (new Bootstrap($this))->boot();
    }

    private function loadAlias()
    {
        foreach ($this->aliasMapping as $key => $alias) {
            foreach ($alias as $alia) {
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