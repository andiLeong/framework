<?php

namespace App\Controller;

use Andileong\Framework\Core\Auth\AuthManager;
use Andileong\Framework\Core\Support\Controller;

class LoginController extends Controller
{

    public function __invoke(AuthManager $auth)
    {
        $credential = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($auth->attempt($credential)) {
            return $auth->user()->only('id', 'remember_token', 'username', 'email', 'name');
        }

        return json([
            'message' => 'your credential is not correct.',
            'code' => 403
        ], 403);
    }
}