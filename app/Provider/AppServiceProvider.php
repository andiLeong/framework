<?php

namespace App\Provider;

use Andileong\Framework\Core\Providers\AbstractProvider;
use Andileong\Framework\Core\Providers\Contract\Provider;
use Andileong\Validation\Validator;

class AppServiceProvider extends AbstractProvider implements Provider
{

    public function register()
    {
        $this->app->bind(Validator::class, fn($app) => new Validator($app['request']->all()));
    }

    public function boot()
    {
        // TODO: Implement boot() method.
    }
}