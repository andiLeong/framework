<?php

namespace Andileong\Framework\Core\Jwt\Contracts;

interface Hash
{
    public function hash($secret, $payload);
}
