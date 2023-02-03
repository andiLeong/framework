<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Hashing\Hasher;
use Andileong\Framework\Core\Hashing\HashManager;

class HashServiceProvider extends AbstractProvider implements Contract\Provider
{
    public function register()
    {
        $this->app->singleton(HashManager::class, fn ($app) => new HashManager($app));
        $this->app->singleton(Hasher::class, fn ($app) => $app['hash']->driver());
    }

    public function boot()
    {
        //
    }
}
