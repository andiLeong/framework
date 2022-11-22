<?php

use Andileong\Framework\Core\Application;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


//$data = [
//    'email' => 'sdsdsd@x.caaabbbbbb',
//    'username' => 'aaaaaaabb',
//    'name' => 'aaaaa',
//    'password' => 'aaaaa@@@@',
//];

$user = new User();
$user->password = 'new ppp';
$user->email = 'new ppp2';
$user->username = 'new dppp';
$user->name = 'new ppp333';

dump($user);
dump($user->save());
dd($user);

//$first = User::whereId(248)->delete();
//$first = new User();
//$first->email = 'saveemail@email.comnew';
//$first->username = 'save usernam@email.comss';
//$first->name = 'saveemail@email.com';

//dd($first);
//dump($first->delete());
//dd($first);

require_once './routes/routes.php';

return $container['router']->run();