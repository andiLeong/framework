<?php

namespace Andileong\Framework\Core;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Framework\Core\Auth\JwtAuth;
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
use Andileong\Framework\Core\Database\Query\Grammar;
use Andileong\Framework\Core\Database\Query\QueryBuilder;
use Andileong\Framework\Core\Exception\Renderer;
use Andileong\Framework\Core\Hashing\Hasher;
use Andileong\Framework\Core\Hashing\HashManager;
use Andileong\Framework\Core\Jwt\Header;
use Andileong\Framework\Core\Jwt\Jwt;
use Andileong\Framework\Core\Logs\LoggerManager;
use Andileong\Framework\Core\Middleware\HandlePreflightRequest;
use Andileong\Framework\Core\Pipeline\Pipeline;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use Andileong\Framework\Core\Support\Cors;
use Andileong\Validation\Validator;
use App\Console\Console;
use App\Exception\Handler;
use App\Middleware\Auth;

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

    public function registerBinding()
    {
        self::$instance = $this;

        $this->bind('app_path', $this->appPath);
        $this->bind('storage_path', $this->appPath . '/storage');
        $this->bind('stubs_path', $this->appPath . '/core/Stubs');
        $this->bind('public_path', $this->appPath . '/public');
        $this->singleton(Request::class, fn() => new Request());
        $this->singleton(Router::class, fn($app) => new Router($app));
        $this->singleton(Connection::class, fn($app) => new Connection($app));
        $this->bind(MysqlConnector::class, fn($app) => new MysqlConnector($app->get('config')));
        $this->bind(RedisConnector::class, fn($app) => new RedisConnector($app->get('config')));
        $this->singleton(RedisConnection::class, fn($app) => new RedisConnection($app));
        $this->singleton(LoggerManager::class, fn($app) => new LoggerManager($app));
        $this->singleton(Console::class, fn($app) => new Console($app));
        $this->singleton(HashManager::class, fn($app) => new HashManager($app));
        $this->singleton(Hasher::class, fn($app) => $app['hash']->driver());
        $this->singleton(CacheManager::class, fn($app) => new CacheManager($app));
        $this->singleton(Cache::class, fn($app) => $app['cache']->driver());
        $this->singleton(CacheHandler::class, fn($app) => $app['cache']->driver());
        $this->bind(Handler::class, function ($app, $args) {
            $renderer = new Renderer($app, $args[0]);
            return new Handler($args[0], $renderer);
        });
        $this->bind(Pipeline::class, fn($app) => new Pipeline($app));
        $this->bind(Auth::class, fn($app) => new Auth($app->get('auth')->guard()));

//        $this->singleton(AuthManager::class, fn($app) => new AuthManager($app));
        $this->bind(Validator::class, fn($app) => new Validator($app['request']->all()));


        $this->singleton(HandlePreflightRequest::class, fn($app) => new HandlePreflightRequest($app));

        $this->bind(Cors::class, fn($app) => new Cors($app['request'], $app['config']));

        $this->bind(QueryBuilder::class, fn($app, $args) => new QueryBuilder(
            $app['db'],
            new Grammar(),
            empty($args) ? null : $args[0]
        ));


        //jwt
//        $this->singleton(Jwt::class, fn($app) => new Jwt($app['config']->get('jwt.secret'), new Header()));
//        $this->singleton(JwtAuth::class, fn($app, $args) => new JwtAuth($app['jwt'], $app['config'], $args[0], $app['cache']));
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

    private function loadServices()
    {
        $providers = $this->get('config')->get('app.providers');

        collection($providers)
            ->map(function ($provider) {
                return new $provider($this);
            })->each(function ($provider) {
                $provider->register();
            })->each(function ($provider) {
                $provider->boot();
            });
    }
}