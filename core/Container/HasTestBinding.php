<?php

namespace Andileong\Framework\Core\Container;

use Andileong\Framework\Core\Container\Exception\InstantiateException;

trait HasTestBinding
{
    protected $testBinding = [];
    protected $isUnitTesting = false;

    /**
     * @param bool $isUnitTesting
     */
    public function setIsUnitTesting(bool $isUnitTesting): void
    {
        $this->isUnitTesting = $isUnitTesting;
    }

    /**
     * @return bool
     */
    public function isUnitTesting(): bool
    {
        return $this->isUnitTesting;
    }

    /**
     * put things to test binding array
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setTestBinding($key, $value)
    {
        return $this->testBinding[$key] = $value;
    }

    /**
     * get thing out of the test binding
     * @param $key
     * @return mixed
     * @throws InstantiateException
     * @throws \ReflectionException
     */
    public function getTestBinding($key)
    {
        if (isset($this->testBinding[$key])) {
            return $this->testBinding[$key];
        }

        if (class_exists($key)) {
            return $this->instantiate($key);
        }

        throw new \InvalidArgumentException($key . ' is not found in the test binding');
    }

}