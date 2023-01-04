<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Auth\Exception\JwtTokenExpiredException;
use Andileong\Framework\Core\Jwt\Jwt;

class JwtAuth
{
    public function __construct(private Jwt $jwt, private array $config)
    {
        //
    }

    /**
     * generate a jwt token
     * @param string|int $user_id
     * @param int|null $expiredTime
     * @param array $payload
     * @param string|null $algorithms
     * @return String
     */
    public function generate(string|int $user_id, int $expiredTime = null, array $payload = [], string $algorithms = null)
    {
        return $this->jwt->generate(
            $this->payload($user_id, $expiredTime, $payload),
            $algorithms ?? $this->getDefaultAlgorithms()
        );
    }

    /**
     * validate the token
     * @param $token
     * @return mixed
     * @throws JwtTokenExpiredException
     */
    public function validate($token)
    {
        $payload = $this->jwt->validate($token);
        if (time() > $payload['expired_at']) {
            throw new JwtTokenExpiredException('Jwt token is expired');
        }
        return $payload['user_id'];
    }

    /**
     * merge the default payload
     * @param string|int $user_id
     * @param int|null $expiredTime
     * @param array $payload
     * @return array
     */
    private function payload(string|int $user_id, ?int $expiredTime, array $payload): array
    {
        return array_merge([
            'user_id' => $user_id,
            'expired_at' => $this->expiredAt($expiredTime),
        ], $payload);
    }

    /**
     * set when the token will expire
     * @param int|null $seconds
     * @return float|int
     */
    private function expiredAt(?int $seconds = null): float|int
    {
        $seconds ??= $this->defaultExpiredTime();
        return time() + $seconds;
    }

    /**
     * get the default expired time stamp default is 3 hours
     * @return float|int
     */
    private function defaultExpiredTime(): float|int
    {
        return $this->config['expire'];
    }

    /**
     * get the default algo
     * @return string
     */
    private function getDefaultAlgorithms()
    {
        return strtolower($this->config['algorithm']);
    }
}