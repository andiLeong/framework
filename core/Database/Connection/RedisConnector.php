<?php

namespace Andileong\Framework\Core\Database\Connection;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Predis\Client;

class RedisConnector
{

    private $host;
    private $port;
    private $scheme;

    public function __construct(Container $container = null)
    {
        $container ??= app();
        $config = $container->get(Config::class);
        $this->host = $config->get('database.connections.redis.host');
        $this->port = $config->get('database.connections.redis.port');
        $this->scheme = $config->get('database.connections.redis.scheme');
    }

    public function connect()
    {
        try {
            return new Client([
                'scheme' => $this->scheme,
                'host' => $this->host,
                'port' => $this->port
            ]);
        } catch (\Throwable $e) {
           throw new \Exception($e->getMessage());
        }
    }
}