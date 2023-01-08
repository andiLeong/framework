<?php

namespace Andileong\Framework\Core\Session;

use Carbon\Carbon;
use Symfony\Component\Finder\Finder;

class FileSessionHandler implements \SessionHandlerInterface
{

    public function __construct(
        protected string $path,
        protected        $expire
    )
    {
        //
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function open(string $path, string $name): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|false
     */
    public function read(string $id): string|false
    {
        $file = $this->getPath($id);
        if (file_exists($file) && !$this->expired($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function write(string $id, string $data): bool
    {
        ensureDirectoryExisted($this->path);
        file_put_contents($this->getPath($id), $data);
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function destroy(string $id): bool
    {
        if (file_exists($path = $this->getPath($id))) {
            return unlink($path);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|false
     */
    public function gc(int $timeToKeep): int|false
    {
        $files = Finder::create()
            ->in($this->path)
            ->files()
            ->date('< now - '.$timeToKeep.' seconds');

        $deletedCount = 0;

        foreach ($files as $file) {
            unlink($file->getRealPath());
            $deletedCount++;
        }

        return $deletedCount;

    }

    /**
     * get the finale path with the file name
     * @param $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->path . '/' . $name;
    }

    /**
     * check the file is expired
     * @param string $file
     * @return bool
     */
    protected function expired(string $file) :bool
    {
        $now = Carbon::now()->getTimestamp();
        $lastModified = filemtime($file);
        $expectedExpireTime = Carbon::createFromTimestamp($lastModified)->addMinutes($this->expire)->getTimestamp();

        if( $now > $expectedExpireTime ){
            return true;
        }

        return false;
    }

}