<?php

namespace Andileong\Framework\Core\Database\Connection;

use Exception;

abstract class AbstractConnection
{
    public function getDriver($driver = null)
    {
        $driver = ucfirst($driver ?? config('database.default')) . 'Connector';
        $class = 'Andileong\\Framework\\Core\\Database\\Connection\\' . $driver;
        if (class_exists($class)) {
            return $this->app->get($class);
        }

        throw new Exception("Driver $driver not supported!");
    }

    public function connect($through = null)
    {
        return $this->getDriver($through)->connect();
    }
}
