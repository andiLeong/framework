<?php

namespace Andileong\Framework\Core\Container;

use Andileong\Framework\Core\Container\Exception\InstantiateException;
use Closure;

class Container implements \ArrayAccess
{
    protected $bindings = [];
    protected $singletons = [];

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

    public function get($key)
    {
        if (!$this->has($key)) {

            if (class_exists($key)) {
                return $this->instantiate($key);
            }

            throw new \Exception("there is no key registered {$key}");
        }

        if ($this->isSingleton($key) && isset($this->singletons[$key])) {
            return $this->singletons[$key];
        }

        $bind = $this->bindings[$key];

        if ($bind['concrete'] instanceof Closure) {

            $concrete = $bind['concrete']($this);
            if ($this->isSingleton($key)) {
                $this->singletons[$key] = $concrete;
            }

            return $concrete;
        }

        return $bind['concrete'];
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

        $dependencies = array_map(function($param){

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

        },$parameters);

        return $reflector->newInstanceArgs($dependencies);

    }

    public function offsetExists(mixed $offset) :Bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset) :mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value) :void
    {
        $this->bind($offset,$value);
    }

    public function offsetUnset(mixed $offset) :void
    {
        unset($this->bindings[$offset]);
    }
}