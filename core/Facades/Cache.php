<?php

namespace Andileong\Framework\Core\Facades;

class Cache extends Facades
{
    public function instance()
    {
        return app('cache');
    }
}
