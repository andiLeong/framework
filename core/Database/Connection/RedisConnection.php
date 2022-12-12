<?php

namespace Andileong\Framework\Core\Database\Connection;

use Andileong\Framework\Core\Application;

class RedisConnection extends AbstractConnection
{
    private static $redis = null;

    public function __construct(protected Application $app)
    {
        //
    }

    public function getRedis()
    {
        if (self::$redis === null) {
            self::$redis = $this->connect('redis');
            return self::$redis;
        }

        return self::$redis;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->getRedis()->{$name}(...$arguments);
    }

}