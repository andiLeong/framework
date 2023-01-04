<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Hash\Hs256Hash;
use Andileong\Framework\Core\Jwt\Hash\Hs384Hash;
use Andileong\Framework\Core\Jwt\Hash\Hs512Hash;

enum Algorithm :String
{
    case HS256 = 'hs256';
    case HS384 = 'hs384';
    case HS512 = 'hs512';

    public function getHash()
    {
        $hash = match ($this) {
            self::HS256 => Hs256Hash::class,
            self::HS384 => Hs384Hash::class,
            self::HS512 => Hs512Hash::class,
        };

        return new $hash();
    }
}
