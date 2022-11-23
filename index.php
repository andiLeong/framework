<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Str;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


$data = [
    'email' => Str::random(13),
    'username' => Str::random(4),
    'name' => 'aaaaa',
    'password' => 'aaaaa@@@@',
];
$user = User::create($data);

dump($user->password);
dd($user);

require_once './routes/routes.php';

return $container['router']->run();