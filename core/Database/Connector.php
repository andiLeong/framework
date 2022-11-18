<?php

namespace Andileong\Framework\Core\Database;

use Andileong\Framework\Core\Container\Container;
use Exception;

class Connector
{
    public function __construct(protected Container $container)
    {
        //
    }

    public function connect()
    {
        return $this->getDriver()->connect();
    }

    public function getDriver()
    {
       $driver = ucfirst(config('database.default')) . 'Connector';
       $class = 'Andileong\\Framework\\Core\\Database\\' . $driver;
       if(class_exists($class)){
            return $this->container->get($class);
       }

       throw new Exception("Driver $driver not supported!");
    }
}