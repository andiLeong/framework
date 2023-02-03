<?php

namespace Andileong\Framework\Core\Hashing;

class BcryptHasher implements Hasher
{
    protected $cost = 10;

    public function __construct(protected array $options = [])
    {
        //
    }

    protected function getOptions($options = null)
    {
        return array_merge([
            'cost' => $this->cost
        ], $options ?? $this->options);
    }

    public function create($value, $options = [])
    {
        return password_hash($value, PASSWORD_BCRYPT, $this->getOptions($options));
    }

    public function needsRehash($hash, $options = [])
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, $this->getOptions($options));
    }

    public function verify($value, $hash)
    {
        return password_verify($value, $hash);
    }
}
