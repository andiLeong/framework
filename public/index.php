<?php

use Andileong\Framework\Core\Application;

require('../vendor/autoload.php');

$app = new Application(dirname(__DIR__));

require_once '../routes/routes.php';

//$cache = $app->get('cache');


//$cache->put('us','us');
//$cache->put('us1','us');
//$cache->put('us2','us');
//$cache->remove();

return $app['router']->response();