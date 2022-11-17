<?php

use Andileong\Framework\Core\Boostrap\LoadConfiguration;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use App\Controller\AboutController;
use App\Controller\ContactController;

require('vendor/autoload.php');

$container = new Container;
$container->singleton('app_path', __DIR__);
$container->singleton(Request::class, fn() => new Request());
$container->bind(LoadConfiguration::class, fn($container) => new LoadConfiguration($container));

$configurationLoader = $container->get(LoadConfiguration::class);
$configurationLoader->boostrap();

$router = new Router($container);

$router->get('', fn() => 'welcome home');
$router->get('about/', [AboutController::class, 'index']);
$router->get('/user/{id}/post/{postId}', [ContactController::class, 'index']);

return $router->run();