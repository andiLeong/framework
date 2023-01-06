<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Framework\Core\Auth\JwtAuth;
use App\Middleware\Auth;

class AuthServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->app->singleton(AuthManager::class, fn($app) => new AuthManager($app));

        $this->app->bind(Auth::class, fn($app) => new Auth($app->get('auth')->guard()));

        $this->app->singleton(JwtAuth::class, fn($app, $args) => new JwtAuth($app['jwt'], $app['config'], $args[0], $app['cache']));
    }

    public function boot()
    {
        //
    }
}