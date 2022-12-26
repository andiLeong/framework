<?php

namespace App\Controller;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Validation\Validator;

class LoginController
{

    public function __invoke(Validator $validator, AuthManager $auth)
    {
//        return json([
//            'message' => 'your credential is not correct.',
//            'code' => 200
//        ], 403, [
////            'Access-Control-Allow-Origin' => '*',
////            'Access-Control-Allow-Credentials' => true,
////            'Access-Control-Allow-Headers' => '*',
////            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
//        ]);

//        return json([
//            'message' => 'your credential is not correct.',
//            'code' => 403
//        ], 403 , [
//            'Access-Control-Allow-Origin' => '*',
//        ]);
//        dd('he');

//        trigger_error("Cannot divide by zero", E_USER_ERROR);
//        return request()->all();

//        return json(request()->all(), 403 , [
//
//            'Access-Control-Allow-Origin' => '*',
//            'Access-Control-Allow-Credentials' => true,
//            'Access-Control-Allow-Headers' => '*',
//            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
//        ]);

        $credential = $validator->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($auth->attempt($credential)) {
            return $auth->user()->only('id', 'remember_token', 'username', 'email', 'name');
        }

        return json([
            'message' => 'your credential is not correct.',
            'code' => 403
        ], 403 , [
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}