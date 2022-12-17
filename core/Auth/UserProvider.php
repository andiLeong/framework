<?php

namespace Andileong\Framework\Core\Auth;

use App\Models\User;

class UserProvider
{
    public function __construct(protected $config)
    {
        //
    }

    public function retrievedByToken(string|null $token)
    {
        return $this->config['model']::where($this->config['column'],$token)->first();
    }
}