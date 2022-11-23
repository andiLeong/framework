<?php

namespace Andileong\Framework\Core\Facades;

/**
 * @method static beginTransaction()
 * @method static transaction(\Closure $param)
 * @method static commit()
 * @method static rollback()
 * @method static setPdo()
 */
class DB extends Facades
{
    public function instance()
    {
        return app('db');
    }
}