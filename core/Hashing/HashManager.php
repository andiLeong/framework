<?php

namespace Andileong\Framework\Core\Hashing;

use Andileong\Framework\Core\Application;

class HashManager
{
    protected $instances = [];

    public function __construct(protected Application $app)
    {
        //
    }

    public function driver($driver = null)
    {
        $default = $this->app->get('config')['hash.driver'];
        $driver ??= $default;

        if (array_key_exists($driver, $this->instances)) {
            return $this->instances[$driver];
        }

        $method = 'create' . ucfirst($driver) . 'Driver';
        if (!method_exists($this, $method)) {
            throw new \Exception('driver ' . $driver . ' not found exception');
        }

        return $this->instances[$driver] = $this->{$method}();
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

    public function __call(string $name, array $arguments)
    {
        return $this->driver()->{$name}(...$arguments);
    }

}