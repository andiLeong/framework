<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Contracts\Hash;
use Andileong\Framework\Core\Jwt\Contracts\Jwt as JwtContract;

class Jwt implements JwtContract
{
    use Base64Encodable;

    /**
     * Jwt constructor.
     * @param $secret
     * @param Header $header
     */
    public function __construct(
        private                 $secret,
        private Header $header,
    )
    {
        //
    }

    /**
     * generate a jwt
     * @param array $payload
     * @param string $algorithms
     * @return String
     */
    public function generate(array $payload, string $algorithms = 'hs256'): string
    {
        $this->header->setAlgo($algorithms);

        $hash = Algorithm::from($algorithms)->getHash();
        $payload = $this->parsePayload($payload);
        return $this->header->get() . "."
            . $payload . "."
            . $this->hash($hash, $payload);
    }

    /**
     * hash header and payload and then base64url encode it
     * @param Hash $hash
     * @param $payload
     * @return string
     */
    protected function hash(Hash $hash, $payload): string
    {
        return $this->encode(
            $hash->hash($this->secret, $this->header->get(), $payload)
        );
    }

    public function parsePayload(array $payload)
    {
        return $this->encode(json_encode(($payload)));
    }

    public function validate(string $token) :bool
    {
        $validator = new ValidateJwtToken();
        return $validator->handle($this->secret,$token);
    }
}