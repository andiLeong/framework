<?php

use Andileong\Framework\Core\Application;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


//$first = User::
//     whereBetween('id',[1,50])
//    ->orderBy('name')
//    ->orderBy('id','desc')
//    whereId('>=',10)
//    ->take('10')
//    ->whereNotNull('created_at')
//    ->where('location' , 'guangzhou')
//    ->first(['id','email']);
//->get();
//dd($first);

require_once './routes/routes.php';

return $container['router']->run();