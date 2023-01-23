<?php

namespace Andileong\Framework\Core\Container;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Container\Exception\InstantiateException;
use Closure;

class Container implements \ArrayAccess
{
    protected $bindings = [];
    protected $singletons = [];
    protected $alias = [];
    protected $aliasMapping = [];
    static protected $instance;

    /**
     * put key as normal non-singleton binding
     * @param $key
     * @param $concrete
     * @param $share
     */
    public function bind($key, $concrete, $share = false)
    {
        $key = $this->getAlias($key);
        $this->bindings[$key] = [
            'concrete' => $concrete,
            'shared' => $share,
        ];
    }

    /**
     * put a key as singleton
     * @param $key
     * @param $concrete
     */
    public function singleton($key, $concrete)
    {
        $this->bind($key, $concrete, true);
    }

    /**
     * determine if the key is bound as singleton
     * @param $key
     * @return mixed
     */
    public function isSingleton($key)
    {
        return $this->bindings[$key]['shared'];
    }

    /**
     * determine if the key is existed in the singletons
     * @param $key
     * @return bool
     */
    public function existedInSingleton($key)
    {
        return isset($this->singletons[$key]);
    }

    /**
     * get all the singleton instances
     * @return array|mixed
     */
    public function getSingleton()
    {
        return $this->singletons;
    }

    /**
     * put key/value to singleton array
     * @param $key
     * @param $concrete
     * @return mixed
     */
    public function setSingleton($key, $concrete)
    {
        $key = $this->getAlias($key);
        return $this->singletons[$key] = $concrete;
    }

    /**
     * try to get a key from the container
     * @param $key
     * @param $args
     * @return mixed|object|string|null
     * @throws InstantiateException
     * @throws \ReflectionException
     */
    public function get($key, $args = [])
    {
        if (is_object($key)) {
            return $key;
        }

        $key = $this->getAlias($key);

        if (!$this->canBePulled($key)) {
            return $this->build($key);
        }

        if ($this->existedInSingleton($key)) {
            return $this->singletons[$key];
        }

        $bind = $this->bindings[$key];

        $concrete = $bind['concrete'] instanceof Closure
            ? $bind['concrete']($this, $args)
            : $bind['concrete'];

        return $this->savedToSingleton($key, $concrete);
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

    /**
     * check if we have key bindings
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->bindings[$key]);
    }

    /**
     * try to instantiate a class
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
            if ($this->canBePulled($dependency)) {
                return $this->get($dependency);
            }

            if (!class_exists($dependency) || (new \ReflectionClass($dependency))->isAbstract()) {
                throw new InstantiateException("We couldn't instantiate for you either dependency is abstract or not instantiable");
            }

            return $this->get($dependency);
//            return $this->instantiate($dependency);

        }, $parameters);

        return $reflector->newInstanceArgs($dependencies);

    }

    /**
     * try to a key's alias if no the key will be return
     * @param $key
     * @return mixed
     */
    public function getAlias($key)
    {
        return $this->alias[$key] ?? $key;
    }

    /**
     * get the application instance
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * set application instance
     * @param Application $instance
     * @return $this
     */
    public function setInstance(Application $instance)
    {
        self::$instance = $instance;
        return $this;
    }

    /**
     * check the key is existed in binding or in singleton instance
     * @param $key
     * @return bool
     */
    protected function canBePulled($key)
    {
        $key = $this->getAlias($key);
        return $this->has($key) || $this->existedInSingleton($key);
    }

    /**
     * try to build a class
     * @param  $class
     * @return mixed|object|string|null
     * @throws InstantiateException
     * @throws \ReflectionException
     */
    protected function build($class)
    {
        if (class_exists($class)) {
            return $this->instantiate($class);
        }

        throw new \Exception("there is no key registered {$class}");
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->canBePulled($offset);
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
        $offset = $this->getAlias($offset);
        unset($this->bindings[$offset]);
        unset($this->singletons[$offset]);
    }
}