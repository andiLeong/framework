<?php

namespace Andileong\Framework\Core\Facades;

use Andileong\Framework\Core\Routing\Router;

/**
 * @method static get(string $string, \Closure|array $param)
 */
class Route implements Facades
{
    public function instance()
    {
        return app()[Router::class];
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $instance = (new static)->instance();
        return [
            $instance,
            $name
        ](...$arguments);
    }
}