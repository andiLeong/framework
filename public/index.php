<?php

use Andileong\Framework\Core\Application;

require('../vendor/autoload.php');

$app = new Application(dirname(__DIR__));

require_once '../routes/routes.php';

return $app['router']->response();
