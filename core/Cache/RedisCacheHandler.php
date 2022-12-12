<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Cache\Contract\Cache;

class RedisCacheHandler extends CacheHandler implements Cache
{
    protected $redis;
    private mixed $prefix;

    public function __construct(protected Application $app)
    {
        $this->redis = $app->get('redis')->getRedis();
        $this->prefix = $app->get('config')['cache.drivers.redis.prefix'];
    }

    public function put($key, $value, $second = 0): bool
    {
        $key = $this->addPrefix($key);
        $second = $this->isDatetimeInterface($second);
        $value = serialize($value);

        if ($second < 0) {
            return false;
        }

        if ($second === 0) {
            $this->redis->set($key, $value);
            return true;
        }

        $this->redis->set($key, $value, 'EX', $second);
        return true;
    }

    public function get($key, $default = null)
    {
        $key = $this->addPrefix($key);
        $value = $this->redis->get($key);
        return $value !== null ? unserialize($value) : $default;
    }

    public function delete($key): bool
    {
        $key = $this->addPrefix($key);
        $this->redis->del($key);
        return true;
    }

    public function remove(): bool
    {
        foreach ($this->redis->keys('*') as $key)
        {
            if(str_starts_with($key, $this->prefix)){
                $this->redis->del($key);
            }
        }
        return true;
    }

    protected function addPrefix($key)
    {
        return $this->prefix . $key;
    }
}