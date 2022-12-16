<?php

namespace Andileong\Framework\Core\Facades;

/**
 * @method static create(string $string)
 * @method static verify(string $string, $res)
 */
class Hash extends Facades
{

    public function instance()
    {
        return app('hash');
    }
}