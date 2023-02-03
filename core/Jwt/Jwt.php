<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Contracts\Hash;
use Andileong\Framework\Core\Jwt\Contracts\Jwt as JwtContract;
use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;

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
    ) {
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
        return $this->formatToken($payload, $hash);
    }

    /**
     * format a jwt token
     * @param $payload
     * @param $hash
     * @return string
     */
    public function formatToken($payload, $hash)
    {
        return $this->header->get() . "." . $payload . "." . $this->hash($hash, $payload);
    }

    /**
     * hash header and payload and then base64url encode it
     * @param Hash $hash
     * @param $payload
     * @return string
     */
    protected function hash(Hash $hash, $payload): string
    {
        $toHash = $this->header->get() . "." . $payload;
        return $this->encode(
            $hash->hash($this->secret, $toHash)
        );
    }

    /**
     * parse the payload
     * @param array $payload
     * @return String
     */
    public function parsePayload(array $payload)
    {
        return $this->encode(json_encode(($payload)));
    }

    /**
     * validate a giving token
     * @param string $token
     * @return array
     * @throws JwtTokenValidationException
     */
    public function validate(string $token) :array
    {
        $validator = new ValidateJwtToken();
        return $validator->handle($this->secret, $token);
    }
}
