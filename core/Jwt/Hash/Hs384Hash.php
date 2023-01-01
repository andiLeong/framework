<?php

namespace Andileong\Framework\Core\Jwt\Hash;

use Andileong\Framework\Core\Jwt\Contracts\Hash;

class Hs384Hash implements Hash
{
    public function hash($secret,$header,$payload)
    {
        return hash_hmac('sha384', $header . "." . $payload, $secret, true);
    }
}