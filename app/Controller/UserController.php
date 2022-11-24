<?php

namespace App\Controller;

use App\Models\User;
use Exception;

class UserController
{
    public function index()
    {
        throw new \InvalidArgumentException('playing with exception is so fun');
        $users = User::select('id','username','email')->paginate(9,'page_name');
        return $users;
    }

    public function show($id)
    {
        return User::find($id);
    }
}