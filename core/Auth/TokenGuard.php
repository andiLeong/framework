<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Auth\Contracts\Guard;
use Andileong\Framework\Core\Hashing\HashManager;
use Andileong\Framework\Core\Request\Request;

class TokenGuard implements Guard
{
    use ValidateUserCredential;

    protected $user;

    public function __construct(protected Request $request, protected UserProvider $provider, protected HashManager $hash)
    {
        //
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function user()
    {
        if($this->user !== null){
            return $this->user;
        }

        $token = $this->request->retrieveBearerToken();
        return $this->user = $this->provider->retrievedByToken($token);
    }

}