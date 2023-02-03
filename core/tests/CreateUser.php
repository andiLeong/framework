<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\tests\stubs\User;
use Andileong\Framework\Core\Support\Str;

trait CreateUser
{
    public function saveUser($username = null, $password = null, $email = null, $name = null)
    {
        $user = new User();
        $user->username = $username ?? Str::random(4);
        $user->password = $password ?? Str::random();
        $user->email = $email ?? Str::random();
        $user->name = $name ?? Str::random(4);

        $result = $user->save();
        return [$user, $result];
    }

    public function baseAttribute($overwrite = [])
    {
        return array_merge([
            'email' => Str::random(5) . '@asd.com',
            'password' => 'mysdsdsd@asd.com',
            'username' => Str::random(),
            'remember_token' => Str::random(),
            'location' => Str::random(4),
            'name' => 'mysdsdsd@asd.com',
        ], $overwrite);
    }

    public function createUser($attributes = [])
    {
        return User::create($this->baseAttribute($attributes));
    }
}
