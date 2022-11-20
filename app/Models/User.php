<?php

namespace App\Models;

use Andileong\Framework\Core\Database\Connection;
use PDO;

class User
{
    protected $connection = null;
    protected $table = 'users';

    public function getTable()
    {
        return $this->table;
    }

    public function getBuilder()
    {
        return $this->getConnection()->builder($this);
    }

    public static function first()
    {
        $stmt = (new static)->getConnection()->prepare("SELECT * FROM users limit 1");
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $all = $stmt->fetch();
        return $all;
    }

    public static function all()
    {

        return (new static)->builder()->get();
        $stmt = (new static)->getConnection()->prepare("SELECT * FROM users");
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $all = $stmt->fetchAll();
        return $all;
    }

    public function getConnection()
    {
        $this->setConnection(new Connection());
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public static function modelInstance()
    {
        return new static;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return [
            self::modelInstance()->getBuilder(),
            $name
        ](...$arguments);
    }

}