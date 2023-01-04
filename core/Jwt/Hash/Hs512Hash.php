<?php

namespace Andileong\Framework\Core\Jwt\Hash;

use Andileong\Framework\Core\Jwt\Contracts\Hash;

class Hs512Hash implements Hash
{

    public function hash($secret, $payload)
    {
        return hash_hmac('sha512', $payload, $secret, true);
    }
}