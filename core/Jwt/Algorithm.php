<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Hash\Hs256Hash;
use Andileong\Framework\Core\Jwt\Hash\Hs384Hash;

enum Algorithm :String
{
    case HS256 = 'hs256';
    case HS384 = 'hs384';

    public function getHash()
    {
        $hash = match ($this) {
            self::HS256 => Hs256Hash::class,
            self::HS384 => Hs384Hash::class,
        };

        return new $hash();
    }
}
