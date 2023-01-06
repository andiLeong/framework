<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Middleware\HandlePreflightRequest;
use Andileong\Framework\Core\Support\Cors;

class CorsServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->app->bind(Cors::class, fn($app) => new Cors($app['request'], $app['config']));
        $this->app->singleton(HandlePreflightRequest::class, fn($app) => new HandlePreflightRequest($app));
    }

    public function boot()
    {
        //
    }
}