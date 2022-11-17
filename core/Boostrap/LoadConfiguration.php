<?php

namespace Andileong\Framework\Core\Boostrap;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Container\Container;
use Symfony\Component\Finder\Finder;

class LoadConfiguration
{

    public function __construct(protected Container $container)
    {
        //
    }


    public function boostrap()
    {
        $config = new Config();
        foreach ($this->fetchConfigFiles() as $file) {

            $path = $file->getRealPath();
            $content = require $path;

            $key = strtolower(basename($path, ".php"));
            $config->set($key, $content);
        }

        $this->container->singleton(Config::class,$config);
    }

    protected function configPath()
    {
        return $this->container->get('app_path') . '/config';
    }

    protected function fetchConfigFiles()
    {
        $finder = new Finder();
        $finder->files()->name('*.php')->in($this->configPath());
        return $finder;
    }
}