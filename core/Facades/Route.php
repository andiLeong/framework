<?php

namespace Andileong\Framework\Core\Facades;

use Andileong\Framework\Core\Routing\Router;

/**
 * @method static get(string $string, \Closure|array $param)
 * @method static post(string $string, \Closure|array $array)
 * @method static middleware(string|array $string)
 * @method static put(string $string, string[] $array)
 * @method static delete(string $string, string[] $array)
 */
class Route extends Facades
{
    public function instance()
    {
        return app('router');
    }
}
