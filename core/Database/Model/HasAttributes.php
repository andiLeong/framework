<?php

namespace Andileong\Framework\Core\Database\Model;

use Andileong\Framework\Core\Support\Str;

trait HasAttributes
{
    public function callMutator($mutator,$value)
    {
        return $this->$mutator($value);
    }

    public function getMutator($key)
    {
        $method = 'set'. Str::camel($key) . 'Attribute';
        if(method_exists($this,$method)){
            return $method;
        }
    }

    protected function getOriginal()
    {
        return $this->originals;
    }

    protected function getAttributes()
    {
        return $this->attributes;
    }

    protected function getDirty()
    {
        return array_diff_assoc($this->attributes, $this->originals);
    }

    protected function setAttribute($key, $value)
    {
        if ($mutator = $this->getMutator($key)) {
            $this->callMutator($mutator,$value);
            return $this;
        }

        $this->attributes[$key] = $value;
        return $this;
    }

    protected function setRawAttributes(array $attributes)
    {
        $this->attributes = $attributes;
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
}