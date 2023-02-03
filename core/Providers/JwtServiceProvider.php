<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Jwt\Header;
use Andileong\Framework\Core\Jwt\Jwt;
use Andileong\Framework\Core\Providers\Contract\Provider;

class JwtServiceProvider extends AbstractProvider implements Provider
{
    public function register()
    {
        $this->app->singleton(Jwt::class, fn ($app) => new Jwt($app['config']->get('jwt.secret'), new Header()));
    }

    public function boot()
    {
        //
    }
}
