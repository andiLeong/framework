<?php

namespace Andileong\Framework\Core\Facades;

class Log extends Facades
{

    public function instance()
    {
        return app('logger');
    }
}