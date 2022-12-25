<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Hashing\HashManager;
use Andileong\Framework\Core\Request\Request;

class TokenGuard implements Auth
{
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

    /**
     * attempt to log in
     * @param array $credential
     * @return boolean
     */
    public function attempt(array $credential)
    {
        $user = $this->provider->retrievedByCredentials($credential);

        if(is_null($user)){
            return false;
        }

        if(!$this->validHash($credential['password'],$user->password)){
            return false;
        }

        $this->user = $user;
        return true;
    }

    /**
     * verify if the hash and plain text are matched
     * @param $value
     * @param $hash
     * @return bool
     */
    protected function validHash($value, $hash): bool
    {
        return $this->hash->verify($value, $hash);
    }
}