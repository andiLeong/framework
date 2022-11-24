<?php

namespace App\Controller;

use App\Models\User;

class UserController
{
    public function index()
    {
        $users = User::select('id','username','email')->paginate(9,'page_name');
        return $users;
    }

    public function show($id)
    {
        return User::find($id);
    }
}