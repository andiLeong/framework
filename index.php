<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Str;
use App\Models\User;

require('vendor/autoload.php');

$container = new Application(__DIR__);



//dump($user->password);
//dd($user);

require_once './routes/routes.php';

return $container['router']->run();