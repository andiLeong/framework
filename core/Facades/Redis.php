<?php

namespace Andileong\Framework\Core\Facades;

class Redis extends Facades
{
    public function instance()
    {
        return app('redis');
    }
}
