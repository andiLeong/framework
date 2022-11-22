<?php

use Andileong\Framework\Core\Application;
use App\Models\User;
require('vendor/autoload.php');

$container = new Application(__DIR__);


//$data = [
//    'email' => 'sdsdsd@x.caaabbbbbbaaaw',
//    'username' => 'aaaaaaabbxcxccc',
//    'name' => 'aaaaa',
//    'password' => 'aaaaa@@@@',
//];

$count = User::whereBetween('id',[10,20])->sum('id', 'sum_id');
//$count = User::whereIn('id',[3,4,5])->count();
dd($count);
//$user = User::create($data);
$user = new User();
$user->password = 'new ppp new222';
//$user->email = 'new ppp2iiiii';
//$user->username = 'new dpppaaa';
//$user->name = 'new ppp333';

//dump($user);
//dump($user->save());
//dump($user->update(['name' => 'facking']));
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