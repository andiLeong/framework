<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;

class ValidateJwtToken
{
    use Base64Encodable;

    /**
     * @param $secret
     * @param $token
     * @return array
     * @throws JwtTokenValidationException
     */
    public function handle($secret, $token) :array
    {
        $token = explode('.', $token);

        if (count($token) != 3) {
            throw new JwtTokenValidationException('A token is not formatted');
        }

        [$header, $payload, $signature] = $token;

        $decodedHeader = $this->decode($header);
        $decodedPayload = $this->decode($payload);

        if (is_null($decodedHeader) ||  is_null($decodedPayload)) {
            throw new JwtTokenValidationException('Invalid payload or header');
        }

        $hash = $this->getHashInstance($decodedHeader['alg']);
        $toHash = $header . "." . $payload;
        $sign = $this->encode(
            $hash->hash($secret, $toHash)
        );

        if ($sign !== $signature) {
            throw new JwtTokenValidationException('Invalid signature');
        }

        return $decodedPayload;
    }

    public function getHashInstance($alg)
    {
        return Algorithm::from(strtolower($alg))->getHash();
    }
}
