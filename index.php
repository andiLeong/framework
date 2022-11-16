<?php

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;
use App\Controller\AboutController;
use App\Controller\ContactController;

require('vendor/autoload.php');

$container = new Container;
$container->singleton(Request::class, fn() => new Request());
//$container->singleton('router', fn($self) => new Route($self));

//$route = $container['router'];
$router = new Router($container);

$router->get('', fn() => 'welcome home');
$router->get('about/', [AboutController::class, 'index']);
$router->get('/user/{id}/post/{postId}', [ContactController::class, 'index']);

return $router->run();