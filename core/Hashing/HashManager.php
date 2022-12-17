<?php

namespace Andileong\Framework\Core\Hashing;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Traits\HasMultipleDrivers;

class HashManager
{
    use HasMultipleDrivers;

    public function __construct(protected Application $app)
    {
        //
    }

    public function createBcryptDriver()
    {
        return new BcryptHasher(
            $this->app->get('config')['hash.bcrypt']
        );
    }

    public function createArgon2iDriver()
    {
        return new Argon2iHasher(
            $this->app->get('config')['hash.argon2i']
        );
    }

    public function getDefaultDriverName()
    {
        return $this->app->get('config')['hash.driver'];
    }
}