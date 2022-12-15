<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Cache\CacheHandler;
use Andileong\Framework\Core\Cache\CacheManager;
use Andileong\Framework\Core\Cache\Contract\Cache;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Database\Connection\MysqlConnector;
use Andileong\Framework\Core\Database\Connection\RedisConnection;
use Andileong\Framework\Core\Database\Connection\RedisConnector;
use Andileong\Framework\Core\Exception\Renderer;
use Andileong\Framework\Core\Logs\LoggerManager;
use Andileong\Framework\Core\Pipeline\Pipeline;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use App\Console\Console;
use App\Exception\Handler;

class Application extends Container
{
    protected $aliasMapping = [
        'app' => [Application::class],
        'request' => [Request::class],
        'router' => [Router::class],
        'config' => [Config::class],
        'db' => [Connection::class],
        'redis' => [RedisConnection::class],
        'exception.handler' => [Handler::class],
        'logger' => [LoggerManager::class],
        'console' => [Console::class],
        'cache' => [CacheManager::class],
    ];

    private $inProduction = false;

    public function __construct(protected $appPath = null)
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
        $this->singleton($this->getAlias(Request::class), fn() => new Request());
        $this->singleton($this->getAlias(Router::class), fn($app) => new Router($app));
        $this->singleton($this->getAlias(Connection::class), fn($app) => new Connection($app));
        $this->bind($this->getAlias(MysqlConnector::class), fn($app) => new MysqlConnector($app->get('config')));
        $this->bind($this->getAlias(RedisConnector::class), fn($app) => new RedisConnector($app->get('config')));
        $this->singleton($this->getAlias(RedisConnection::class), fn($app) => new RedisConnection($app));
        $this->singleton($this->getAlias(LoggerManager::class), fn($app) => new LoggerManager($app));
        $this->singleton($this->getAlias(Console::class), fn($app) => new Console($app));
        $this->singleton($this->getAlias(CacheManager::class), fn($app) => new CacheManager($app));
        $this->singleton(Cache::class, fn($app) => $app['cache']->driver());
        $this->singleton(CacheHandler::class, fn($app) => $app['cache']->driver());
        $this->bind($this->getAlias(Handler::class), function ($app, $args) {
            $renderer = new Renderer($app,$args[0]);
            return new Handler($args[0],$renderer);
        });
        $this->bind($this->getAlias(Pipeline::class), fn($app) => new Pipeline($app));
    }

    protected function boot()
    {
        (new Bootstrap($this))->boot();
    }

    protected function loadAlias()
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