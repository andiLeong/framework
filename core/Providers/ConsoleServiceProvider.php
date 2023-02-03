<?php

namespace Andileong\Framework\Core\Providers;

use App\Console\Console;

class ConsoleServiceProvider extends AbstractProvider implements Contract\Provider
{
    public function register()
    {
        $this->app->singleton(Console::class, fn ($app) => new Console($app));
    }

    public function boot()
    {
        //
    }
}
