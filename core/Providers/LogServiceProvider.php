<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Logs\LoggerManager;

class LogServiceProvider extends AbstractProvider implements Contract\Provider
{
    public function register()
    {
        $this->app->singleton(LoggerManager::class, fn ($app) => new LoggerManager($app));
    }

    public function boot()
    {
        //
    }
}
