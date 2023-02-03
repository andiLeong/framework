<?php

namespace Andileong\Framework\Core\Providers;

use Andileong\Framework\Core\Application;

abstract class AbstractProvider
{
    public function __construct(protected Application $app)
    {
        //
    }
}
