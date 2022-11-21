<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Framework\Core\Database\Connection\Connection;

abstract class Model
{
    protected $connection = null;
    protected $table = null;
    protected $attributes = [];
    protected $existed = false;
    protected $primaryKey = 'id';

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getTable()
    {
        return $this->table ?? strtolower(classBaseName($this) . 's');
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getBuilder()
    {
        return $this->getConnection()->builder($this);
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

    /**
     * get a new instance of a model when hydrate
     * @param $attributes
     * @return $this
     */
    public function newInstance($attributes = [])
    {
        $new = new static;
        $new->setAttributes($attributes);
        $new->setConnection(
            $this->connection
        );
        $new->setTable(
            $this->getTable()
        );
        $new->existed = true;

        return $new;
    }

    public static function query()
    {
        $instance = self::modelInstance();
        return $instance->getBuilder();
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $instance = self::modelInstance();

        return [
            $instance->getBuilder(),
            $name
        ](...$arguments);
    }

}