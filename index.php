<?php

use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Route;
use App\Controller\AboutController;
use App\Controller\ContactController;

require('vendor/autoload.php');

$request = new Request();
$route = new Route($request);

$route->get('/',fn() => 'welcome home');
$route->get('about', [AboutController::class,'index']);
$route->get('/contact/{id}', [ContactController::class,'index']);

$content = $route->run();

echo $content;
//dd($route::$routes);