<?php

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Route;
use App\Controller\AboutController;
use App\Controller\ContactController;

require('vendor/autoload.php');

$container = new Container;
$container->singleton(Request::class, fn() => new Request());
//$container->singleton('router', fn($self) => new Route($self));

//$route = $container['router'];
$route = new Route($container);

$route->get('',fn() => 'welcome home');
$route->get('about/', [AboutController::class,'index']);
$route->get('/contact/{id}', [ContactController::class,'index']);

$content = $route->run();

echo $content;
//dd($route::$routes);