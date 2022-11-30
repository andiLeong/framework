<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Cache\Contract\CacheStore;
use Symfony\Component\Finder\Finder;

class FileCacheHandler implements CacheStore
{
    protected $directory;

    public function __construct(protected Application $app)
    {
        $this->directory = $this->app->get('storage_path') . '/framework/cache';
    }

    /**
     * store a key into cache
     * @param $key
     * @param $value
     * @param $second
     * @return bool
     */
    public function put($key, $value, $second = 0): bool
    {
        $time = $this->generateTimestamp($second);

        try {
            ensureDirectoryExisted($this->directory);
            file_put_contents(
                $this->path(md5($key)),
                $this->content($value, $time)
            );
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * get cache by its key
     * @param $key
     * @param $default
     * @return mixed|string
     */
    public function get($key, $default = null)
    {
        if (!file_exists($path = $this->path(md5($key)))) {
            return 'file not exist';
            return $default;
        }

        $content = @file_get_contents($path);
        if (!$content) {
            return 'cant get content';
            return $default;
        }

        [$time, $content] = $this->extractContent($content);

//        dd([$time, $content]);
        if ($this->isExpired($time)) {
            $this->delete($key);
            dump('expired delete the cache');
            return 'expired';
        }


        return unserialize($content);
    }

    /**
     * extract time and cache content from a file content
     * @param $content
     * @return array
     */
    protected function extractContent($content)
    {
        if (str_starts_with($content, 0)) {
            return [0, substr($content, 1)];
        }
        return [
            substr($content, 0, 10),
            substr($content, 10)
        ];
    }

    /**
     * get the path of a cache file
     * @param $name
     * @return string
     */
    protected function path($name)
    {
        return $this->directory . '/' . $name;
    }

    /**
     * generate the cache content
     * @param $value
     * @param $time
     * @return string
     */
    private function content($value, $time)
    {
        return $time . serialize($value);
    }

    /**
     * generate the cache timestamp
     * @param $second
     * @return mixed
     */
    protected function generateTimestamp($second)
    {
        if ($second == 0) {
            return $second;
        }

        $time = time() + $second;
        return min($time, 9999999999);
    }

    /**
     * check a certain timestamp is expired
     * @param $time
     * @return bool
     */
    protected function isExpired($time)
    {
        if ($time == 0) {
            return true;
        }
        return time() >= $time;
    }

    /**
     * check if key existed in cache
     * @param $key
     * @return bool
     */
    public function has($key) :bool
    {
        return $this->get($key) !== null;
    }

    /**
     * store a cache item forever
     * @param $key
     * @param $value
     * @return bool
     */
    public function forever($key, $value) :bool
    {
        return $this->put($key, $value);
    }

    /**
     * store array of keys
     * @param array $values
     * @param $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds = 0) :bool
    {
        foreach ($values as $key => $value) {
            if(!$this->put($key, $value, $seconds)){
                return false;
            }
        }

        return true;
    }

    /**
     * delete a cache item
     * @param $key
     * @return bool
     */
    public function delete($key): bool
    {
        if (file_exists($path = $this->path(md5($key)))) {
            return @unlink($path);
        }

        return false;
    }

    /**
     * remove all the cache items
     *
     */
    public function remove() :bool
    {
        if (!is_dir($this->directory)) {
            return false;
        }

        $finder = new Finder();
        $files = $finder->files()->in($this->directory);
        foreach ($files as $file) {
            if (!@unlink($file)) {
                return false;
            }
        }

        return true;
    }
}