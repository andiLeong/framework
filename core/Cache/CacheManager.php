<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Application;

class CacheManager
{
    protected $instances = [];

    public function __construct(protected Application $app)
    {
        //
    }

    public function driver($driver = null)
    {
        $default = $this->app->get('config')['cache']['default'];
        $driver ??= $default;

        if(array_key_exists($driver,$this->instances)){
           return $this->instances[$driver];
        }

        $method = 'create'. ucfirst($driver) .'Driver';
        if (!method_exists($this,$method)) {
            throw new \Exception('driver ' . $driver . ' not found exception');
        }

        return $this->instances[$driver] = $this->{$method}();
    }

    protected function createFileDriver()
    {
        return new FileCacheHandler($this->app);
    }

    protected function createArrayDriver()
    {
        return new ArrayCacheHandler();
    }

    protected function createRedisDriver()
    {
        return new RedisCacheHandler($this->app);
    }

    public function __call(string $name, array $arguments)
    {
        return [$this->driver(),$name](...$arguments);
    }
}