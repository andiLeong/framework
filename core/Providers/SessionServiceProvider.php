<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Cookie\CookieJar;
use Andileong\Framework\Core\Session\Middleware\StartSession;
use Andileong\Framework\Core\Session\SessionManager;
use Andileong\Framework\Core\Session\Store;

class SessionServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->registerManager();
        $this->registerStore();
        $this->registerStartSessionMiddleware();
    }

    public function boot()
    {
        //
    }

    private function registerStartSessionMiddleware()
    {
        $this->app->singleton(StartSession::class, function () {
            $manager = $this->app->get(SessionManager::class);
            $cookieJar = $this->app->get(CookieJar::class);
            return new StartSession($manager, $cookieJar);
        });
    }

    private function registerManager()
    {
        $this->app->singleton(SessionManager::class, fn() => new SessionManager($this->app->get('config')['session'])
        );
    }

    private function registerStore()
    {
        $this->app->singleton(Store::class, fn($app) => $app->get('session.manager')->driver()
        );
    }
}