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
     * @param array $attributes
     * @param bool $existed
     * @return $this
     */
    public function newInstance($attributes = [], $existed = true)
    {
        //todo keep tract of original data set
        $new = new static;
        $new->setAttributes($attributes);
        $new->setConnection(
            $this->connection
        );
        $new->setTable(
            $this->getTable()
        );
        $new->existed = $existed;

        return $new;
    }

    public static function query()
    {
        $instance = self::modelInstance();
        return $instance->getBuilder();
    }

    public static function create(array $attributes)
    {
        $instance = self::modelInstance();
        $id = $instance->query()->insert($attributes);

        return $instance->newInstance(array_merge([
            $instance->getPrimaryKey() => $id
        ], $attributes));
    }

    public function save()
    {
        if ($this->existed) {
            return $this->toUpdate();
        }

        return $this->toSave();
    }

    private function toUpdate()
    {
        $newAttributes = $this->attributes;
        $res = $this->getBuilder()->update(
            //todo get the difference attributes then update
            array_filter($this->attributes,fn($attribute,$key) => $key !== $this->getPrimaryKey(),ARRAY_FILTER_USE_BOTH)
        );

        if($res){
            $this->setAttributes($newAttributes);
        }

        return $res;
    }

    private function toSave()
    {
        $id = $this->getBuilder()->insert($this->attributes);
        $this->existed = true;

        $this->setAttributes(
            array_merge([
                $this->getPrimaryKey() => $id
            ], $this->attributes)
        );
        return true;
    }

    public function delete()
    {
        if($this->existed === false){
            throw new \LogicException('Model does not existed');
        }

        $res = $this->getBuilder()->delete($this->attributes[$this->getPrimaryKey()]);
        if($res){
            $this->setAttributes([]);
            $this->existed = false;

            return $res;
        }

        return false;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
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