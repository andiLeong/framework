<?php

namespace Andileong\Framework\Core\Facades;

use Andileong\Framework\Core\Config\Config as ConfigRepository;

class Config extends Facades
{
    public function instance()
    {
        return app()[ConfigRepository::class];
    }
}