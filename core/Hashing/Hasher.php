<?php

namespace Andileong\Framework\Core\Hashing;

interface Hasher
{
    public function create($value, $options = []);

    public function needsRehash($hash, $options = []);

    public function verify($value, $hash);
}
