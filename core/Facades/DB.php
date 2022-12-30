<?php

namespace Andileong\Framework\Core\Facades;

/**
 * @method static beginTransaction()
 * @method static transaction(\Closure $param)
 * @method static commit()
 * @method static rollback()
 * @method static setPdo()
 * @method static from(string $string)
 * @method static select(string $string, string $string1)
 * @see \Andileong\Framework\Core\Database\Connection\Connection
 */
class DB extends Facades
{
    public function instance()
    {
        return app('db');
    }
}