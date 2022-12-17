<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Request\Request;

class TokenGuard implements Auth
{
    protected $user;

    public function __construct(protected Request $request, protected UserProvider $provider)
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

        $token = $this->retrieveToken();
        return $this->user = $this->provider->retrievedByToken($token);
    }

    private function retrieveToken()
    {
        if($token = $this->request->bearerToken()){
           return $token;
        }

        return $this->request->get('token');
    }
}