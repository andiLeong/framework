<?php

use Andileong\Framework\Core\Application;

require('../vendor/autoload.php');

$app = new Application(dirname(__DIR__));


if ($app['request']->method() === 'OPTIONS') {
    $response = json('', 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Credentials' => true,
        'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
    ]);

    return $response->send();
}

require_once '../routes/routes.php';


return $app['router']->response();