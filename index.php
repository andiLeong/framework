<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Str;
use App\Models\User;

require('vendor/autoload.php');

$app = new Application(__DIR__);

//$e = new Exception();
//
//dump($app->get('exception.handler',[$e]));
//dd($app);

require_once './routes/routes.php';

return $app['router']->run();