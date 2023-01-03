<?php

namespace Andileong\Framework\Core\Support\Traits;

trait HasMultipleDrivers
{
    protected $instances = [];

    abstract public function getDefaultDriverName();

    public function driver($driver = null)
    {
        $driver ??= $this->getDefaultDriverName();

        if (array_key_exists($driver, $this->instances)) {
            return $this->instances[$driver];
        }

        $method = 'create' . ucfirst($driver) . 'Driver';
        if (!method_exists($this, $method)) {
            throw new \Exception('driver ' . $driver . ' not found');
        }

        return $this->instances[$driver] = $this->{$method}();
    }

    public function __call(string $name, array $arguments)
    {
        return [$this->driver(), $name](...$arguments);
    }
}