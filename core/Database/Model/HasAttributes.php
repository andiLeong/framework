<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\Support\Str;

trait HasAttributes
{
    public function callAccessor($accessor, $value)
    {
        return $this->$accessor($value);
    }

    public function getAccessor($key)
    {
        $method = 'get' . Str::camel($key) . 'Attribute';
        return hasMethodDefined($this, $method);
    }

    public function callMutator($mutator, $value)
    {
        return $this->$mutator($value);
    }

    public function getMutator($key)
    {
        $method = 'set' . Str::camel($key) . 'Attribute';
        return hasMethodDefined($this, $method);
    }

    protected function getOriginal()
    {
        return $this->originals;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    protected function getAttribute($name)
    {
        $value = $this->attributes[$name];
        if ($accessor = $this->getAccessor($name)) {
            return $this->callAccessor($accessor, $value);
        }
        return $value;
    }

    protected function getDirty()
    {
        return array_diff_assoc($this->attributes, $this->originals);
    }

    protected function setAttribute($key, $value)
    {
        if ($mutator = $this->getMutator($key)) {
            $this->callMutator($mutator, $value);
            return $this;
        }

        $this->attributes[$key] = $value;
        return $this;
    }

    protected function setRawAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    protected function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $attr) {
            $this->setAttribute($key, $attr);
        }
    }

    protected function syncOriginals(array $values = null)
    {
        $values ??= $this->attributes;
        $this->originals = $values;
    }

    protected function syncChanges($changes)
    {
        $this->changes = $changes;
    }

    protected function toArray()
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            $attributes[$key] = $this->getAttribute($key);
        }

        foreach ($this->getAppends() as $value) {
            if ($accessor = $this->getAccessor($value)) {
                $attributes[$value] = $this->callAccessor($accessor, $value);
            }
        }

        return $attributes;
    }

    /**
     * get a subset of model attributes back
     * @param $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return Arr::only($this->toArray(),$keys);
    }

    protected function getAppends()
    {
        return $this->appends;
    }
}