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
     * @return string
     */
    public static function before(string $string, string $search)
    {
        if ($search === '') {
            return $string;
        }

        $value = mb_strstr($string, $search, true);
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
        if ($search === '') {
            return $string;
        }

        $result = strstr($string, $search);
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
     * remove part of the string
     * @param string $string
     * @param string $search
     * @return array|string|string[]
     */
    public static function remove(string $string, string $search)
    {
        return self::replace($string, $search);
    }

    /**
     * remove first occurrence of a string
     * @param string $string
     * @param string $search
     * @return array|string|string[]
     */
    public static function removeFirst(string $string, string $search)
    {
        return self::replaceFirst($string, $search);
    }

    /**
     * replace any occurrence
     * @param string $string
     * @param string $search
     * @param string $replacement
     * @return array|string|string[]
     */
    public static function replace(string $string, string $search, string $replacement = '')
    {
        return str_replace($search, $replacement, $string);
    }

    /**
     * replace first occurrence of a string
     * @param string $string
     * @param string $search
     * @param string $replacement
     * @return array|string|string[]
     */
    public static function replaceFirst(string $string, string $search, string $replacement = '')
    {
        if ($search === '') {
            return $string;
        }

        $pos = strpos($string, $search);
        if ($pos === false) {
            return $string;
        }

        return substr_replace($string, $replacement, $pos, strlen($search));
    }

    /**
     * limit the length of the given string
     * @param string $string
     * @param int $length
     * @param string $end
     * @return string
     */
    public static function limit(string $string, int $length, string $end = '...')
    {
        $string = mb_substr($string, 0, $length);
        return $string . $end;
    }

    /**
     * mask a string with given characters
     * @param string $string
     * @param string $replacement
     * @param int $index
     * @param int|null $length
     * @return array|string|string[]
     */
    public static function mask(string $string, string $replacement, int $index, int $length = null)
    {
        $indexInPositive = abs($index);
        $stringLength = strlen($string);

        if ($index === 0 || $replacement === '' || $indexInPositive > $stringLength) {
            return $string;
        }

        $bounce = $index > 0
            ? $stringLength - $index
            : $indexInPositive;

        if (is_null($length) || $length > $bounce) {
            $length = $bounce;
        }

        return substr_replace($string, str_repeat($replacement, $length), $index, $length);
    }

    /**
     * reverse the string
     * @param string $string
     * @return string
     */
    public static function reverse(string $string)
    {
        return implode(array_reverse(mb_str_split($string)));
    }

    /**
     * make a string title case
     * @param string $string
     * @return array|false|string|string[]|null
     */
    public static function title(string $string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * calculate the words of a string
     * @param string $string
     * @return int
     */
    public static function wordCount(string $string)
    {
        return str_word_count($string);
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

    /**
     * generate a ulid string
     * @return Ulid
     * @throws \Exception
     */
    public static function ulid($uppercase = true)
    {
        return Ulid::generate($uppercase);
    }

    /**
     * determine a given id is a valid ulid
     * @param string $ulid
     * @return bool
     */
    public static function isUlid(string $ulid)
    {
        return Ulid::isValid($ulid);
    }
}