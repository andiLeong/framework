<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Auth\Exception\JwtTokenExpiredException;
use Andileong\Framework\Core\Cache\CacheManager;
use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;
use Andileong\Framework\Core\Jwt\Jwt;

class JwtAuth
{
    use TokenCanBeCache;

    protected $jwtConfig;
    protected $defaultExpire;

    public function __construct(private Jwt $jwt, private Config $config, private string $guard, private CacheManager $cache)
    {
        $this->jwtConfig = $this->config->get('jwt');
        $this->defaultExpire = $this->config->get("auth.guards.$this->guard.expire");
    }

    /**
     * generate a jwt token
     * @param string|int $userId
     * @param int|null $expiredTime
     * @param array $payload
     * @param string|null $algorithms
     * @return String
     */
    public function generate(string|int $userId, int $expiredTime = null, array $payload = [], string $algorithms = null)
    {
        $token = $this->jwt->generate(
            $this->payload($userId, $expiredTime, $payload),
            $algorithms ?? $this->getDefaultAlgorithms()
        );

        $this->cache->put($this->cacheKey($userId), $token);

        return $token;
    }

    /**
     * validate the token
     * @param $token
     * @return mixed
     * @throws JwtTokenExpiredException|JwtTokenValidationException
     */
    public function validate($token)
    {
        $payload = $this->jwt->validate($token);
        $userId = $payload['user_id'];
        $tokenInCache = $this->handleTokenDiscarded($userId, $token);

        if ($this->tokenExpired($payload['expired_at'])) {
            $this->removeCacheToken($userId, $tokenInCache);
            throw new JwtTokenExpiredException('Jwt token is expired');
        }

        return $userId;
    }

    /**
     * merge the default payload
     * @param string|int $userId
     * @param int|null $expiredTime
     * @param array $payload
     * @return array
     */
    private function payload(string|int $userId, ?int $expiredTime, array $payload): array
    {
        return array_merge([
            'user_id' => $userId,
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
        $seconds ??= $this->defaultExpire;
        return time() + $seconds;
    }

    /**
     * get the default algo
     * @return string
     */
    private function getDefaultAlgorithms()
    {
        return strtolower($this->jwtConfig['algorithm']);
    }

    /**
     * @param $expired_at
     * @return bool
     */
    protected function tokenExpired($expired_at): bool
    {
        return time() > $expired_at;
    }
}
