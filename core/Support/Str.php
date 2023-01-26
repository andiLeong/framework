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

    /**
     * generate a fake uuid version 4
     * @return string
     * @throws \Exception
     */
    public static function uuid4()
    {
        $str = bin2hex(random_bytes(16));
        return self::separate($str, '-', [8, 4, 4, 4, 12]);
    }

    /**
     * separate a string by a separator
     * @param $string
     * @param $separator
     * @param $portions
     * @return string
     */
    public static function separate($string, $separator = '-', $portions = [])
    {
        $array = str_split($string);
        $result = '';
        foreach ($array as $index => $value) {
            $index = $index + 1;

            //if $portion array is empty
            //we assume developer wants to separate every value in the string
            if(empty($portions) && $index != count($array)){
                $result .= $value . $separator;
                continue;
            }

            //if we are on the last loop
            //we just append the value without any separator
            if ($index === count($array)) {
                $result .= $value;
                continue;
            }

            // here we check if the index equals to the potion fist value
            // if yes we append the separator, we also calculate the next 2 index and push the portion first value
            // so whenever $portions first value is always updated index in the array
            if ($index == $portions[0]) {

                $value = $value . $separator;

                if (count($portions) >= 2) {
                    $nextIndex = $portions[0] + $portions[1];
                    unset($portions[0]);
                    unset($portions[1]);
                    array_unshift($portions, $nextIndex);
//                    dump($portions);
                }
            }

            $result .= $value;
        }

        return $result;
    }

    /**
     * decide the given string is uuid
     * @param $uuid
     * @return bool
     */
    public static function isUuid($uuid)
    {
        return preg_match('/[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}/', $uuid) > 0;
    }
}