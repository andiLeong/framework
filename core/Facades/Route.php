<?php

namespace Andileong\Framework\Core\Facades;

use Andileong\Framework\Core\Routing\Router;

/**
 * @method static get(string $string, \Closure|array $param)
 */
class Route extends Facades
{
    public function instance()
    {
        return app()[Router::class];
    }
}