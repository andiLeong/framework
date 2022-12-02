<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Config\Config;
use Symfony\Component\Finder\Finder;

class LoadConfiguration
{
    private Application $app;

    public function bootstrap(Application $app)
    {
        $this->app = $app;

        $config = new Config();
        foreach ($this->fetchConfigFiles() as $file) {

            $path = $file->getRealPath();
            $content = require $path;

            $key = strtolower(basename($path, ".php"));
            $config->set($key, $content);
        }

        $app->singleton($app->getAlias(Config::class), $config);

        date_default_timezone_set($config->get('app.timezone','Asia/Hong_Kong'));
    }

    protected function configPath()
    {
        return $this->app->get('app_path') . '/config';
    }

    protected function fetchConfigFiles()
    {
        $finder = new Finder();
        $finder->files()->name('*.php')->in($this->configPath());
        return $finder;
    }
}