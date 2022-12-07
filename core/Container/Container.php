<?php

namespace Andileong\Framework\Core\Container;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Container\Exception\InstantiateException;
use Closure;

class Container implements \ArrayAccess
{
    use HasTestBinding;

    protected $bindings = [];
    protected $singletons = [];
    protected $alias = [];
    protected $aliasMapping = [];
    static protected $instance;

    public function bind($key, $concrete, $share = false)
    {
        $this->bindings[$key] = [
            'concrete' => $concrete,
            'shared' => $share,
        ];
    }

    public function singleton($key, $concrete)
    {
        $this->bind($key, $concrete, true);
    }

    public function isSingleton($key)
    {
        return $this->bindings[$key]['shared'];
    }

    public function existedInSingleton($key)
    {
        return isset($this->singletons[$key]);
    }

    public function getSingleton()
    {
        return $this->singletons;
    }

    public function setSingleton($key,$concrete)
    {
        $key = $this->getAlias($key);
        return $this->singletons[$key] = $concrete;
    }

    public function get($key, $args = [])
    {
        $key = $this->getAlias($key);

        if($this->isUnitTesting()){
           return $this->getTestBinding($key);
        }

        if (!$this->has($key) && !$this->existedInSingleton($key)) {

            if (class_exists($key)) {
                return $this->instantiate($key);
            }

            throw new \Exception("there is no key registered {$key}");
        }

        if ($this->existedInSingleton($key)) {
            return $this->singletons[$key];
        }

        $bind = $this->bindings[$key];


        $concrete = $bind['concrete'] instanceof Closure
            ? $bind['concrete']($this, $args)
            : $bind['concrete'];

        return $this->savedToSingleton(
            $key,
            $concrete
        );
    }

    /**
     * try to save to the singleton cache and return
     * @param $key
     * @param $concrete
     */
    protected function savedToSingleton($key, $concrete)
    {
        if ($this->isSingleton($key)) {
            $this->singletons[$key] = $concrete;
        }
        return $concrete;
    }

    public function has($key)
    {
        return isset($this->bindings[$key]);
    }

    /**
     * @param string $class
     * @return mixed|object|string|null
     * @throws InstantiateException
     * @throws \ReflectionException
     */
    private function instantiate(string $class)
    {
        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class();
        }

        $parameters = $constructor->getParameters();

        $dependencies = array_map(function ($param) {

            if ($param->isDefaultValueAvailable()) {
                return $param->getDefaultValue();
            }

            $type = $param->getType();

            if (is_null($type)) {
                $message = "We encounter one of the constructor type is not specify we couldn't instantiate for you";
                throw new InstantiateException($message);
            }

            if ($type instanceof \ReflectionUnionType) {
                throw new InstantiateException("Target class contains union type argument , we can't instantiate for you");
            }

            $dependency = $param->getType()->getName();
            if (!class_exists($dependency) || (new \ReflectionClass($dependency))->isAbstract()) {
                throw new InstantiateException("wec couldn't instantiate for you either dependency is abstract or not instantiable");
            }

            return $this->get($dependency);
//            return $this->instantiate($dependency);

        }, $parameters);

        return $reflector->newInstanceArgs($dependencies);

    }

    public function getAlias($key)
    {
        return $this->alias[$key] ?? $key;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function setInstance(Application $instance)
    {
        self::$instance = $instance;
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->bind($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->bindings[$offset]);
    }
}