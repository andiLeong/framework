<?php

namespace Andileong\Framework\Core\Support;

class Str
{

    public static function camel($value, $separator = '_')
    {
        $capitalizedArray = array_map(
            fn($value) => ucfirst($value),
            explode($separator, $value)
        );
        return implode('', $capitalizedArray);
    }
}