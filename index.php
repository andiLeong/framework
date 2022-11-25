<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Str;
use App\Models\User;

require('vendor/autoload.php');

$app = new Application(__DIR__);

require_once './routes/routes.php';

return $app['router']->run();