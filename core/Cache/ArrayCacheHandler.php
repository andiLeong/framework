<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Cache\Contract\Cache;

class ArrayCacheHandler extends CacheHandler implements Cache
{
    protected $items = [];

    public function put($key, $value, $second = 0): bool
    {
        $time = $this->generateTimestamp($second);

        $this->items[$key] = [
            'expire' => $time,
            'value' => serialize($value),
        ];
        return true;
    }

    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->items)) {
            return $default;
        }

        $item = $this->items[$key];
        if($this->isExpired($item['expire'])){
            $this->delete($key);
            return $default;
        }

        return unserialize($this->items[$key]['value']);
    }

    public function delete($key): bool
    {
        unset($this->items[$key]);
        return true;
    }

    public function remove(): bool
    {
        $this->items = [];
        return true;
    }

    public function isExpired($time)
    {
        if ($time == 0) {
            return true;
        }
        return time() >= $time;
    }
}