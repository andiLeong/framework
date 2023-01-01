<?php

namespace Andileong\Framework\Core\Jwt;

use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;

class ValidateJwtToken
{
    use Base64Encodable;

    public function __construct(
//        private $secret,
//        private $token,
    )
    {
        //
    }

    public function handle($secret, $token)
    {
        $token = explode('.', $token);

        if (count($token) != 3) {
            throw new JwtTokenValidationException('A token is not formatted');
        }

        [$header, $payload, $signature] = $token;

        $decodedHeader = $this->decode($header);
        $decodedPayload = $this->decode($payload);

        $hash = $this->getHashInstance($decodedHeader['alg']);
        $sign = $this->encode(
            $hash->hash($secret, $header, $payload)
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