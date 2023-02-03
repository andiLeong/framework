<?php

namespace Andileong\Framework\Core\Jwt\Hash;

use Andileong\Framework\Core\Jwt\Contracts\Hash;

class Hs256Hash implements Hash
{
    public function hash($secret, $payload)
    {
        return hash_hmac('sha256', $payload, $secret, true);
    }
}
