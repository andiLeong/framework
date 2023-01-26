<?php

namespace Andileong\Framework\Core\Support;

class Str
{

    /**
     * generate a random string based on the length
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function random(int $length = 10)
    {
        $bytes = random_bytes($length);
        return substr(str_replace(['+', '/', '='], '', base64_encode($bytes)), 0, $length);
    }

    /**
     * convert a string to camel case
     * @param string $value
     * @param string $separator
     * @return string
     */
    public static function camel(string $value, string $separator = '_')
    {
        $capitalizedArray = array_map(
            fn($value) => ucfirst($value),
            explode($separator, $value)
        );
        return implode('', $capitalizedArray);
    }

    /**
     * convert string to kebab-case
     * @param string $string
     * @param string $separator
     * @return string
     */
    public static function kebab(string $string, string $separator = '-')
    {
        $string = str_replace(' ', '', $string);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', $separator . '$0', $string));
    }

    /**
     * convert string to snake_case
     * @param string $string
     * @return string
     */
    public static function snake(string $string)
    {
        return self::kebab($string, '_');
    }

    /**
     * find a portion of string before a certain string
     * @param string $string
     * @param string $search
     * @param bool $before
     * @return string
     */
    public static function before(string $string, string $search, bool $before = true)
    {
        if (trim($search) === '') {
            return $string;
        }

        $value = strstr($string, trim($search), $before);
        return $value === false ? $string : $value;
    }

    /**
     * try to get a portion of string after a search
     * @param string $string
     * @param string $search
     * @return string
     */
    public static function after(string $string, string $search)
    {
        if (trim($search) === '') {
            return $string;
        }

        $result = strstr($string, trim($search));
        return $result === false
            ? $string
            : ltrim($result, $search);
    }

    /**
     * search part of the string between 2 strings
     * @param string $string
     * @param string $first
     * @param string $second
     * @return string
     */
    public static function between(string $string, string $first, string $second)
    {
        $trimSuffice = self::before($string, $second);
        if ($trimSuffice === $string) {
            return $string;
        }

        $value = self::after($trimSuffice, $first);
        if ($value === $trimSuffice) {
            return $string;
        }
        return $value;
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
     * @param string $string
     * @param string $separator
     * @param array $portions
     * @return string
     */
    public static function separate(string $string, string $separator = '-', array $portions = [])
    {
        $array = str_split($string);
        $result = '';
        foreach ($array as $index => $value) {
            $index = $index + 1;

            //if $portion array is empty
            //we assume developer wants to separate every value in the string
            if (empty($portions) && $index != count($array)) {
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
     * @param string $uuid
     * @return bool
     */
    public static function isUuid(string $uuid)
    {
        return preg_match('/[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}/', $uuid) > 0;
    }
}