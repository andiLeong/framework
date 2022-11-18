<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Routing\Router;

require('vendor/autoload.php');

$container = new Application(__DIR__);

require_once './routes/routes.php';

return $container[Router::class]->run();