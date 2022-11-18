<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Application;
use Dotenv\Dotenv;

class SetEnvironmentVariable
{
    public function bootstrap(Application $app)
    {
        $dotenv = Dotenv::createImmutable($app->get('app_path'));
        $dotenv->safeLoad();
    }
}
