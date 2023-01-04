<?php

namespace Andileong\Framework\Core\Jwt\Contracts;

interface Jwt
{
    public function generate(array $payload, string $algorithms = 'hs256'): string;

    public function validate(string $token): array;
}