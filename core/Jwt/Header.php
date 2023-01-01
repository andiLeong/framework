<?php

namespace Andileong\Framework\Core\Jwt;

class Header
{
    use Base64Encodable;

    private $alg = 'HS256';


    public function get()
    {
        return $this->encode(json_encode(($this->header())));
    }

    public function header()
    {
        return [
            'alg' => $this->alg,
            'typ' => 'JWT',
        ];
    }

    public function setAlgo(string $algorithms)
    {
        $this->alg = strtoupper(Algorithm::from($algorithms)->value);
        return $this;
    }

}