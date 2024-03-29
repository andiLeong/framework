<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Database\Model\ModelCollection;
use Andileong\Framework\Core\Database\Model\Paginator;
use Carbon\Carbon;

abstract class CacheHandler
{
    /**
     * check if key existed in cache
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * store a cache item forever
     * @param $key
     * @param $value
     * @return bool
     */
    public function forever($key, $value): bool
    {
        return $this->put($key, $value);
    }

    /**
     * store array of keys
     * @param array $values
     * @param $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds = 0): bool
    {
        foreach ($values as $key => $value) {
            if (!$this->put($key, $value, $seconds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * get the timestamp where will store
     * @param $key
     * @param $second
     * @return false|void
     */
    protected function getTimestamp($key, $second)
    {
        try {
            $timestamp = $this->generateTimestamp($second);
        } catch (\InvalidArgumentException $e) {
            $this->delete($key);
            return false;
        }

        return $timestamp;
    }

    /**
     * generate the cache timestamp
     * @param mixed $second
     * @return int|mixed
     */
    protected function generateTimestamp(mixed $second): mixed
    {
        $second = $this->isDatetimeInterface($second);

        if ($second == 0) {
            return $second;
        }

        if ($second > 0) {
            return time() + $second;
        }

        throw new \InvalidArgumentException('cache time can not be less than a second');
    }

    /**
     * try to get cache item , if not found store to cache
     * @param $key
     * @param $seconds
     * @param $default
     * @return null
     */
    public function remember($key, $seconds, $default)
    {
        $result = $this->get($key);
        if (!is_null($result)) {
            return $result;
        }

        $value = value($default);
        $this->put($key, $value, $seconds);

        return $value;
    }

    /**
     * store to cache item forever if key isn't found
     * @param $key
     * @param $default
     * @return null
     */
    public function rememberForever($key, $default)
    {
        return $this->remember($key, 0, $default);
    }

    /**
     * detect if the time pass is datetime interface
     * @param mixed $second
     * @return float|int|mixed
     */
    protected function isDatetimeInterface(mixed $second): mixed
    {
        if ($second instanceof \DateTimeInterface) {
            $second = Carbon::now()->diffInSeconds($second, false);
        }
        return $second;
    }

    protected function serializedValue($value)
    {
        if ($value instanceof Model) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof Paginator || $value instanceof ModelCollection) {
            $value = $value->toJson();
        }

        return $value;
    }
}
