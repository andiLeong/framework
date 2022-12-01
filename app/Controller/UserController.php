<?php

namespace App\Controller;

use Andileong\Framework\Core\Cache\Contract\Cache;
use App\Models\User;

class UserController
{
    public function index(Cache $cache)
    {
        return file_get_contents(storagePath().'/framework/cache/index.php');
        return $cache->get('users','default');
        return $cache->remember('users',2*60,function(){
            return User::get();
        });
    }

    public function show($id)
    {
        return User::find($id);
    }
}