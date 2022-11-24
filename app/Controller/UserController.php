<?php

namespace App\Controller;

use App\Models\User;

class UserController
{

    public function show($id)
    {
        return User::find($id);
    }
}