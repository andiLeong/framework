<?php

namespace Andileong\Framework\Core\Database;

use Andileong\Framework\Core\Models\QueryBuilder;
use Exception;

class Connection
{
    protected ?QueryBuilder $builder = null;

    public function __construct()
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
        if (class_exists($class)) {
            return new $class();
        }

        throw new Exception("Driver $driver not supported!");
    }

    public function builder($model)
    {
        $this->builder = new QueryBuilder($this, new Grammar(), $model);
        return $this->builder;
    }
}