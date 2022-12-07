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
     * @return bool
     */
    public function setTestBinding($key, $value)
    {
        if (!empty($filtered = $this->getAliasMappingFor($key))) {

            $alias = $this->flattenAliasItem($filtered);
            foreach ($alias as $aliasKey) {
                $this->testBinding[$aliasKey] = $value;
            }
            return true;
        }

        $this->testBinding[$key] = $value;
        return true;
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

    /**
     * get all the register alias for a key
     * @param $key
     * @return array|mixed
     */
    protected function getAliasMappingFor($key): mixed
    {
        return array_filter($this->aliasMapping, fn($value, $aliasKey) => $aliasKey == $key || in_array($key, $value),
            ARRAY_FILTER_USE_BOTH);
    }

    /**
     * flatten an alias array
     * @param mixed $aliasItem
     * @return array
     */
    protected function flattenAliasItem(mixed $aliasItem): array
    {
        $alias = [];
        foreach ($aliasItem as $name => $aliasArray) {
            $alias[] = $name;
            foreach ($aliasArray as $aliasValue) {
                $alias[] = $aliasValue;
            }
        }
        return $alias;
    }

}