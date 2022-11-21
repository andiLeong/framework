<?php

namespace Andileong\Framework\Core\Database\Connection;

use Andileong\Framework\Core\Database\Query\Grammar;
use Andileong\Framework\Core\Database\Query\QueryBuilder;
use Exception;
use PDO;

class Connection
{
    protected ?QueryBuilder $builder = null;
//    protected $fetchMode = PDO::FETCH_OBJ;
    protected static PDO|null $pdo = null;

    public function __construct()
    {
        //
    }

    public function getPdo()
    {
        if (self::$pdo === null) {
            self::$pdo = $this->getDriver()->connect();
            return self::$pdo;
        }

        return self::$pdo;
    }

    public function getDriver()
    {
        $driver = ucfirst(config('database.default')) . 'Connector';
        $class = 'Andileong\\Framework\\Core\\Database\\Connection\\' . $driver;
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

    public function runSelect($query, $bindings = [])
    {
//        dump($query);
//        dump($bindings);

        $stmt = $this->getPdo()->prepare($query);

        $bindings
            ? $stmt->execute($bindings)
            : $stmt->execute();

        return $stmt->fetchAll();
    }

    public function runInsert($query, array $bindings)
    {
//        dump($query);
//        dump($bindings);
        $pdo = $this->getPdo();
        $pdo->prepare($query)->execute($bindings);
        return $pdo->lastInsertId();
    }

    public function runUpdate(string $query, array $bindings)
    {
//        dump($query);
//        dump($bindings);
        return $this->getPdo()->prepare($query)->execute($bindings);
    }

    public function runDelete(string $query, array $bindings)
    {
        dump($query);
        dump($bindings);

        $stmt = $this->getPdo()->prepare($query);
        $bindings
            ? $stmt->execute($bindings)
            : $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}