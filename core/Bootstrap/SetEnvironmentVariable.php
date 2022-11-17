<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Container\Container;
use Dotenv\Dotenv;

class SetEnvironmentVariable
{
    public function bootstrap(Container $container)
    {
        $dotenv = Dotenv::createImmutable($container->get('app_path'));
        $dotenv->safeLoad();
    }
}
