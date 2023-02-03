<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Support\Arr;

class UserProvider
{
    public function __construct(protected $config)
    {
        //
    }

    public function retrievedByToken(string|null $token)
    {
        return $this->config['model']::where($this->config['column'], $token)->first();
    }

    public function retrievedByCredentials(array $credential)
    {
        $credential = Arr::only($credential, ['email']);
        if (empty($credential)) {
            return;
        }

        return $this->config['model']::where('email', $credential['email'])->first();
    }

    public function retrievedById(mixed $userId)
    {
        return $this->config['model']::findOrFail($userId);
    }
}
