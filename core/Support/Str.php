<?php

namespace Andileong\Framework\Core\Support;

class Str
{

    public static function random($length = 10)
    {
        $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($x, ceil($length / strlen($x)))), 1, $length);
    }

    public static function camel($value, $separator = '_')
    {
        $capitalizedArray = array_map(
            fn($value) => ucfirst($value),
            explode($separator, $value)
        );
        return implode('', $capitalizedArray);
    }
}