<?php

namespace App\Controller;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Validation\Validator;

class LoginController
{

    public function __invoke(Validator $validator, AuthManager $auth)
    {
        $credential = $validator->validate([
            'email' => 'required|email',
            'password' => 'required',
            'foo' => 'required',
        ]);

        if($auth->attempt($credential)){
            return $auth->user()->only('id','remember_token','username','email','name');
        }

        return json([
            'message' => 'your credential is not correct.',
            'code' => 403
        ],403);
    }
}