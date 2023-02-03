<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Cache\CacheHandler;
use Andileong\Framework\Core\Cache\CacheManager;
use Andileong\Framework\Core\Cache\Contract\Cache;

class CacheServiceProvider extends AbstractProvider implements Contract\Provider
{
    public function register()
    {
        $this->app->singleton(CacheManager::class, fn ($app) => new CacheManager($app));
        $this->app->singleton(Cache::class, fn ($app) => $app['cache']->driver());
        $this->app->singleton(CacheHandler::class, fn ($app) => $app['cache']->driver());
    }

    public function boot()
    {
        //
    }
}
