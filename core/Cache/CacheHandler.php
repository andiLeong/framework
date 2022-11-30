<?php

namespace Andileong\Framework\Core\Cache;

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
     * generate the cache timestamp
     * @param mixed $second
     * @return int|mixed
     */
    protected function generateTimestamp(mixed $second): mixed
    {
        if ($second == 0) {
            return $second;
        }
        return time() + $second;
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
}