<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Framework\Core\Auth\JwtAuth;
use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Cache\CacheManager;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Cors\Cors;
use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Database\Connection\RedisConnection;
use Andileong\Framework\Core\Database\Query\QueryBuilder;
use Andileong\Framework\Core\Exception\Renderer;
use Andileong\Framework\Core\Hashing\HashManager;
use Andileong\Framework\Core\Jwt\Jwt;
use Andileong\Framework\Core\Logs\LoggerManager;
use Andileong\Framework\Core\Pipeline\Pipeline;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use Andileong\Framework\Core\Session\SessionCookie;
use Andileong\Framework\Core\Session\SessionManager;
use Andileong\Framework\Core\Session\Store;
use Andileong\Validation\Validator;
use App\Console\Console;
use App\Exception\Handler;
use App\Middleware\CreateCookies;

class Application extends Container
{
    protected $aliasMapping = [
        'app' => [Application::class],
        'request' => [Request::class],
        'router' => [Router::class],
        'config' => [Config::class],
        'db' => [Connection::class],
        'builder' => [QueryBuilder::class],
        'redis' => [RedisConnection::class],
        'exception.handler' => [Handler::class],
        'logger' => [LoggerManager::class],
        'console' => [Console::class],
        'cache' => [CacheManager::class],
        'hash' => [HashManager::class],
        'auth' => [AuthManager::class],
        'validator' => [Validator::class],
        'cors' => [Cors::class],
        'jwt' => [Jwt::class],
        'jwt.auth' => [JwtAuth::class],
        'session.manager' => [SessionManager::class],
        'session' => [Store::class],
    ];

    private $inProduction = false;

    public function __construct(protected $appPath = null)
    {
        $this->loadAlias();
        $this->registerBinding();
        $this->boot();
        $this->setInProduction();
        $this->loadServices();
    }

    /**
     * register all base binding
     */
    public function registerBinding()
    {
        self::$instance = $this;

        $this->bind('app_path', $this->appPath);
        $this->bind('storage_path', $this->appPath . '/storage');
        $this->bind('stubs_path', $this->appPath . '/core/Stubs');
        $this->bind('public_path', $this->appPath . '/public');
        $this->singleton(Request::class, fn() => new Request());
        $this->singleton(Router::class, fn($app) => new Router($app));
        $this->bind(Handler::class, function ($app, $args) {
            $renderer = new Renderer($app, $args[0]);
            return new Handler($args[0], $renderer);
        });
        $this->bind(Pipeline::class, fn($app) => new Pipeline($app));



        $this->singleton(CreateCookies::class, fn($app) => new CreateCookies(
           $app->get(SessionCookie::class)
        ));
    }

    /**
     * start to boot up the application
     */
    protected function boot()
    {
        (new Bootstrap($this))->boot();
    }

    /**
     * load all the alias mapping to alias array
     */
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

    /**
     * load all the service providers and register its bindings
     * @throws \Exception
     */
    protected function loadServices()
    {
        $providers = $this->get('config')->get('app.providers');

        collection($providers)
            ->map(fn($provider) => new $provider($this))
            ->each(function ($provider) {
                $provider->register();
            })->each(function ($provider) {
                $provider->boot();
            });
    }
}