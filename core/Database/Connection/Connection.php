<?php

namespace Andileong\Framework\Core\Database\Connection;

use Andileong\Framework\Core\Database\Query\Grammar;
use Andileong\Framework\Core\Database\Query\QueryBuilder;
use Exception;
use PDO;

class Connection
{
    protected ?QueryBuilder $builder = null;
    protected static PDO|null $pdo = null;

    public function __construct()
    {
        //
    }

    public function transaction(callable $fn)
    {
        $pdo = $this->getPdo();
        try {
            $pdo->beginTransaction();
            $result = $fn();
            $pdo->commit();
            return $result;
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function beginTransaction()
    {
        $this->getPdo()->beginTransaction();
    }

    public function rollback()
    {
        $this->getPdo()->rollBack();
    }

    public function commit()
    {
        $this->getPdo()->commit();
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
        $stmt->execute($bindings);

        return $stmt->fetchAll();
    }

    public function runAggregate($query, $bindings = [])
    {
//        dump($query);
//        dump($bindings);
        $stmt = $this->getPdo()->prepare($query);

        $stmt->execute($bindings);
        return $stmt->fetchColumn();
    }

    public function runInsert($query, array $bindings)
    {
//        dump($query);
//        dump($bindings);
        $pdo = $this->getPdo();
//        dump($pdo);
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
//        dump($query);
//        dump($bindings);

        $stmt = $this->getPdo()->prepare($query);
        $bindings
            ? $stmt->execute($bindings)
            : $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}