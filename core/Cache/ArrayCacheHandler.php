<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Cache\Contract\Cache;

class ArrayCacheHandler implements Cache
{
    protected $items = [
//        'users' => [
//            'expire' => 0,
//            'value' => 'sth'
//        ]
    ];

    public function __construct(protected Application $app)
    {
        //
    }

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

        return unserialize($this->items[$key]['value'])[0];
    }

    public function has($key): bool
    {
        return $this->get($key) !== null;
    }

    public function forever($key, $value): bool
    {
        return $this->put($key, $value);
    }

    public function putMany(array $values, $seconds = 0): bool
    {
        foreach ($values as $key => $value){
            if(!$this->put($key,$value,$seconds)){
                return false;
            }
        }

        return true;
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

    /**
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

    public function isExpired($time)
    {
        if ($time == 0) {
            return true;
        }
        return time() >= $time;
    }
}