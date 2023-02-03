<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Auth\Exception\JwtTokenExpiredException;

trait TokenCanBeCache
{

    /**
     * handle the token was discarded
     * @param $usrId
     * @param $token
     * @return mixed
     * @throws JwtTokenExpiredException
     */
    protected function handleTokenDiscarded($usrId, $token)
    {
        $tokenInCache = $this->getFromCache($usrId);

        if ($tokenInCache && $tokenInCache !== $token) {
            throw new JwtTokenExpiredException('Jwt token was discard');
        }

        return $tokenInCache;
    }

    /**
     * remove the token from the cache
     * @param $userId
     * @param $tokenInCache
     */
    protected function removeCacheToken($userId, $tokenInCache): void
    {
        if ($tokenInCache) {
            $this->cache->delete($this->cacheKey($userId));
        }
    }

    /**
     * get the token key in the cache
     * @param $userId
     * @return string
     */
    protected function cacheKey($userId)
    {
        return "jwt.$userId";
    }

    /**
     * get the ite from cache
     * @param $userId
     * @return mixed
     */
    protected function getFromCache($userId)
    {
        return $this->cache->get($this->cacheKey($userId));
    }
}
