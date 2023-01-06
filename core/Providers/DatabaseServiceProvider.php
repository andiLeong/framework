<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Database\Connection\Connection;
use Andileong\Framework\Core\Database\Connection\MysqlConnector;
use Andileong\Framework\Core\Database\Connection\RedisConnection;
use Andileong\Framework\Core\Database\Connection\RedisConnector;
use Andileong\Framework\Core\Database\Query\Grammar;
use Andileong\Framework\Core\Database\Query\QueryBuilder;

class DatabaseServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->app->singleton(Connection::class, fn($app) => new Connection($app));
        $this->app->bind(MysqlConnector::class, fn($app) => new MysqlConnector($app->get('config')));
        $this->app->bind(RedisConnector::class, fn($app) => new RedisConnector($app->get('config')));
        $this->app->singleton(RedisConnection::class, fn($app) => new RedisConnection($app));

        $this->app->bind(QueryBuilder::class, fn($app, $args) => new QueryBuilder(
            $app['db'],
            new Grammar(),
            empty($args) ? null : $args[0]
        ));
    }

    public function boot()
    {
        //
    }
}