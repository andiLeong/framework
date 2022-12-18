<?php

namespace Andileong\Framework\Core\Cache;

use Andileong\Framework\Core\Cache\Contract\Cache;
use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Database\Model\Paginator;
use Symfony\Component\Finder\Finder;

class FileCacheHandler extends CacheHandler implements Cache
{
    private $expiredKeys = [];

    public function __construct(protected $directory)
    {
        //
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
        $time = $this->getTimestamp($key, $second);

        try {
            ensureDirectoryExisted($this->directory);
            file_put_contents(
                $this->path(md5($key)),
                $this->content($value, $time)
            );
        } catch (\Exception $e) {
            throw $e;
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
            return value($default);
        }

        $content = @file_get_contents($path);
        if (!$content) {
            return value($default);
        }

        [$time, $content] = $this->extractContent($content);

        if ($this->isExpired($time, $key)) {
            $this->delete($key);
            return value($default);
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
        $value = $this->serializedValue($value);
        return $time . serialize($value);
    }

    /**
     * check a certain timestamp is expired
     * @param $time
     * @param null $key
     * @return bool
     */
    protected function isExpired($time, $key = null)
    {
        if (in_array($key, $this->expiredKeys)) {
            $this->expiredKeys = [];
            return true;
        }

        if ($time == 0) {
            return false;
        }
        return time() >= $time;
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
    public function remove(): bool
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

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setKeyToExpired($key)
    {
        $this->expiredKeys[] = $key;
    }
}