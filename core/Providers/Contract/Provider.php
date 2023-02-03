<?php

namespace Andileong\Framework\Core\Providers\Contract;

interface Provider
{
    public function register();
    public function boot();
}
