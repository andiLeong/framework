<?php

namespace App\Controller;

use Andileong\Framework\Core\Cache\Contract\Cache;
use App\Models\User;
use Carbon\Carbon;

class UserController
{
    public function index(Cache $cache)
    {
        return $cache->remember('users',Carbon::now()->addMinutes(2),function(){
            return User::get();
        });
    }

    public function show($id)
    {
        return User::find($id);
    }
}