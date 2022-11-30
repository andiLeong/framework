<?php

namespace Andileong\Framework\Core\Cache\Contract;

interface Cache
{

    public function put($key, $value, $second = 0): bool;

    public function get($key, $default = null);

    public function has($key): bool;

    public function forever($key, $value): bool;

    public function putMany(array $values, $seconds = 0): bool;

    public function delete($key): bool;

    public function remove(): bool;
}