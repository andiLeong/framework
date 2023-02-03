<?php

namespace Andileong\Framework\Core\Facades;

abstract class Facades
{
    abstract public function instance();

    public static function __callStatic(string $name, array $arguments)
    {
        $instance = (new static)->instance();
        return [
            $instance,
            $name
        ](...$arguments);
    }
}
