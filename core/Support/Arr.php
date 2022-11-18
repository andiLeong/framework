<?php

namespace Andileong\Framework\Core\Support;

class Arr
{
    //hasAny

    public static function has($arr, $key)
    {
        if (!str_contains($key, '.')) {
            return isset($arr[$key]);
        }

        $keys = explode('.', $key);
        $first = array_shift($keys);

        if (!isset($arr[$first])) {
            return false;
        }

        $tem = $arr[$first];

        foreach ($keys as $k) {
            if (!isset($tem[$k])) {
                return false;
            }
            $tem = $tem[$k];
        }

        return true;
    }



    /**
     * fetch the last item from the array
     * @param $arr
     * @param $fn
     * @param $default
     * @return mixed|null
     */
    public static function last($arr, $fn = null, $default = null)
    {
        return self::first(array_reverse($arr), $fn, $default);
    }

    /**
     * fetch first item from array , if callback provided it will trigger callback to determine
     * @param array $arr
     * @param null $fn
     * @param null $default
     * @return mixed|null
     */
    public static function first(array $arr, $fn = null, $default = null)
    {
        if (count($arr) === 0) {
            return $default;
        }

        if ($fn !== null) {
            $filtered = array_values(array_filter($arr, fn($value) => $fn($value)));
            if (count($filtered) === 0) {
                return $default;
            }

            return array_shift($filtered);
        }

        return $arr[array_key_first($arr)];
    }

    /**
     * return certain part of the array based on the keys passed
     * @param $arr
     * @param $keys
     * @return array
     */
    public static function only($arr, $keys)
    {
        return array_intersect_key($arr, array_flip(self::wrap($keys)));
    }

    /**
     * wrap string to array
     * @param $thing
     * @return array|null[]
     */
    public static function wrap($thing = null)
    {
        if($thing === null){
            return [];
        }

        if (is_array($thing)) {
            return $thing;
        }

        return [$thing];
    }

    /**
     * get valur from an array support dot notation
     * @param $arr
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public static function get($arr, $key, $default = null)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }

        if (str_contains($key, '.')) {
            $keys = explode('.', $key);

            return array_reduce($keys, function ($carry, $item) use ($default) {
                if (isset($carry[$item])) {
                    $carry = $carry[$item];
                    return $carry;
                }
                return $default;
            }, $arr);
        }

        return $default;
    }
}