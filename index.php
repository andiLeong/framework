<?php

use Andileong\Framework\Core\Application;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


//$first = User::
//     whereBetween('id',[1,9])
//    whereId('>=',10)
//    ->take('10')
//    ->whereNotNull('created_at')
//    ->where('location' , 'guangzhou')
//    ->first(['id','email']);
//all();
//dd($first);

require_once './routes/routes.php';

return $container['router']->run();