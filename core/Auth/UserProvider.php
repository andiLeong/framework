<?php

namespace Andileong\Framework\Core\Auth;

use App\Models\User;

class UserProvider
{

    public function retrievedByToken(string|null $token)
    {
        return User::where('remember_token',$token)->first();
    }
}