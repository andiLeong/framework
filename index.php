<?php

use Andileong\Framework\Core\Application;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


//$first = User::
//     whereBetween('id',[254,255])
//    ->delete(253);
//
//
//dd($first);

require_once './routes/routes.php';

return $container['router']->run();