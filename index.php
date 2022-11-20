<?php

use Andileong\Framework\Core\Application;
use App\Models\User;

require('vendor/autoload.php');

$container = new Application(__DIR__);


//$all = User::all();
//$first = User::whereName('andi')->where('id', '>' , 3)->get();
//$first = User::select('email','id')->whereName('andi')->where('id', '>' , 3);
//$second = User::whereName('andi');
//dump($second);
//dd($first);

require_once './routes/routes.php';

return $container['router']->run();