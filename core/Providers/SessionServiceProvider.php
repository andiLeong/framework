<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Session\SessionManager;
use Andileong\Framework\Core\Session\Store;

class SessionServiceProvider extends AbstractProvider implements Contract\Provider
{

    public function register()
    {
        $this->registerManager();
        $this->registerStore();
    }

    public function boot()
    {
        //
    }

    private function registerManager()
    {
        $this->app->singleton(SessionManager::class, fn() =>
            new SessionManager($this->app->get('config')['session'])
        );
    }

    private function registerStore()
    {
        $this->app->singleton(Store::class,
            fn($app) => $app->get('session.manager')->driver()
        );
    }
}