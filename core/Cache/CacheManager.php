<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Traits\HasMultipleDrivers;

class CacheManager
{
    use HasMultipleDrivers;

    public function __construct(protected Application $app)
    {
        //
    }

    protected function createFileDriver()
    {
        $directory = $this->app->get('storage_path') . '/framework/cache';
        return new FileCacheHandler($directory);
    }

    protected function createArrayDriver()
    {
        return new ArrayCacheHandler();
    }

    protected function createRedisDriver()
    {
        $redis = $this->app->get('redis')->getRedis();
        $prefix = $this->app->get('config')['cache.drivers.redis.prefix'];
        return new RedisCacheHandler($redis, $prefix);
    }

    public function getDefaultDriverName()
    {
        return $this->app->get('config')['cache']['default'];
    }
}