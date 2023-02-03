<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Cookie\CookieJar;

class CookieServiceProvider extends AbstractProvider implements Contract\Provider
{
    public function register()
    {
        $this->app->singleton(CookieJar::class, fn ($app) => new CookieJar());
    }

    public function boot()
    {
        //
    }
}
