<?php

namespace Andileong\Framework\Core\Facades;

use Andileong\Framework\Core\Routing\Router;

/**
 * @method static get(string $string, \Closure|array $param)
 * @method static post(string $string, string[] $array)
 * @method static middleware(string|array $string)
 */
class Route extends Facades
{
    public function instance()
    {
        return app('router');
    }
}