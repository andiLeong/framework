<?php

use Andileong\Framework\Core\Application;

require('../vendor/autoload.php');

$app = new Application(dirname(__DIR__));

require_once '../routes/routes.php';

header("Access-Control-Allow-Origin: *");

return $app['router']->response();