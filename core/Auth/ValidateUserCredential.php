<?php

namespace Andileong\Framework\Core\Auth;

trait ValidateUserCredential
{
    /**
     * attempt to log in
     * @param array $credential
     * @return boolean
     */
    public function attempt(array $credential)
    {
        $user = $this->provider->retrievedByCredentials($credential);

        if (is_null($user)) {
            return false;
        }

        if (!$this->validHash($credential['password'], $user->password)) {
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
