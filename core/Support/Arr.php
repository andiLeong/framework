<?php

namespace Andileong\Framework\Core\Support;

class Arr
{

    /**
     * get valur from an array support dot notation
     * @param $arr
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public static function get($arr, $key, $default = null)
    {
        if(isset($arr[$key])){
            return $arr[$key];
        }

        if(str_contains($key,'.')){
            $keys = explode('.',$key);

            return array_reduce($keys,function($carry,$item) use($default){
                if(isset($carry[$item])){
                    $carry = $carry[$item];
                    return $carry;
                }
                return $default;
            },$arr);
        }

        return $default;
    }
}