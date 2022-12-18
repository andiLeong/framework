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
            return User::latest()->paginate(10);
        });
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function store()
    {
        
    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }
}