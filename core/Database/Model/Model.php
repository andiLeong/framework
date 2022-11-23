<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Framework\Core\Database\Connection\Connection;

abstract class Model
{
    use HasAttributes;

    protected $connection = null;
    protected $table = null;
    protected $attributes = [];
    protected $originals = [];
    protected $changes = [];
    protected $existed = false;
    protected $primaryKey = 'id';

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return bool
     */
    public function isExisted(): bool
    {
        return $this->existed;
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
        $new = new static;
        $new->setRawAttributes($attributes);
        $new->setConnection(
            $this->connection
        );
        $new->setTable(
            $this->getTable()
        );
        $new->existed = $existed;
        $new->syncOriginals();

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

    public function update(array $attributes)
    {
        foreach ($attributes as $name => $value){
            $this->setAttribute($name,$value);
        }

        return $this->toUpdate();
    }

    public function save()
    {
        return $this->existed
            ? $this->toUpdate()
            : $this->toSave();
    }

    private function toUpdate()
    {
        $newAttributes = $this->getDirty();
        if(empty($newAttributes) || $this->existed === false){
            return false;
        }

        $res = $this->toUpdateSql()->update($newAttributes);
        if($res){
            $this->syncOriginals();
            $this->syncChanges($newAttributes);
        }

        return $res;
    }

    protected function toUpdateSql()
    {
        $key = $this->getPrimaryKey();
        return $this->getBuilder()->where($key,$this->attributes[$key]);
    }

    private function toSave()
    {
        $id = $this->getBuilder()->insert($this->attributes);
        $this->existed = true;

        $this->setAttribute($this->getPrimaryKey(),$id);
        $this->syncOriginals();
        return true;
    }

    public function delete()
    {
        if($this->existed === false){
            throw new \LogicException('Model does not existed');
        }

        $res = $this->getBuilder()->delete($this->attributes[$this->getPrimaryKey()]);
        if($res){
            $this->existed = false;
            return $res;
        }

        return false;
    }

    public function __set(string $name, $value): void
    {
        $this->setAttribute($name,$value);
    }

    public function __get(string $name)
    {
        return $this->getAttribute($name);
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