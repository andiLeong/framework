<?php

namespace Andileong\Framework\Core\Jwt;

trait Base64Encodable
{

    /**
     * base64 url encode
     * @param string $value
     * @return String
     */
    protected function encode(string $value) :String
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'),'=');
//        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($value));
    }

    /**
     * decode the base64url encoded
     * @param string $value
     * @return mixed
     */
    public function decode(string $value)
    {
        return json_decode(base64_decode(strtr($value, '-_', '+/')), true);
    }
}