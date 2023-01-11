<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Session\Middleware\StartSession;
use Andileong\Framework\Core\Session\SessionCookie;
use Andileong\Framework\Core\Session\SessionManager;
use Andileong\Framework\Core\Session\Store;

class SessionServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->registerManager();
        $this->registerStore();
        $this->registerStartSessionMiddleware();

        $this->app->singleton(SessionCookie::class, fn($app) =>
            new SessionCookie($app['request'],$app['session'],$this->app->get('config')['session'])
        );
    }

    public function boot()
    {
        //
    }

    private function registerStartSessionMiddleware()
    {
        $this->app->singleton(StartSession::class, fn($app) =>
            new StartSession($this->app->get(SessionManager::class))
        );
    }

    private function registerManager()
    {
        $this->app->singleton(SessionManager::class, fn() =>
            new SessionManager($this->app->get('config')['session'])
        );
    }

    private function registerStore()
    {
        $this->app->singleton(Store::class, fn($app) =>
            $app->get('session.manager')->driver()
        );
    }
}