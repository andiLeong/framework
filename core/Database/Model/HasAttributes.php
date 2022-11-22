<?php

namespace Andileong\Framework\Core\Database\Model;

trait HasAttributes
{

    protected function getDirty()
    {
        return array_diff_assoc($this->attributes, $this->originals);
    }

    protected function setAttribute($key, $value)
    {
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