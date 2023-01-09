<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Session\Middleware\StartSession;
use Andileong\Framework\Core\Session\SessionManager;
use Andileong\Framework\Core\Session\Store;

class SessionServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->app->singleton(SessionManager::class, fn() =>
            new SessionManager($this->app->get('config')['session'])
        );

        $this->app->singleton(Store::class, fn($app) =>
            $app->get('session.manager')->driver()
        );

        $this->app->singleton(StartSession::class, fn($app) =>
            new StartSession($this->app->get(SessionManager::class))
        );
    }

    public function boot()
    {
        //
    }
}