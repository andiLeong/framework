<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Facades\Config as ConfigFacades;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class LoadConfigurationTest extends testcase
{

    /** @test */
    public function it_can_load_the_configuration_files_and_save_to_container()
    {
        $finder = new Finder();
        $finder->files()->name('*.php')->in($_SERVER['DOCUMENT_ROOT'] . '/config');

        $config = new Config();
        foreach ($finder as $file) {

            $path = $file->getRealPath();
            $content = require $path;

            $key = strtolower(basename($path, ".php"));
            $config->set($key, $content);
        }

        $this->assertEquals('mysql',$config->get('database.default'));
    }

    /** @test */
    public function it_get_a_single_config_value_from_helper_and_facades()
    {
        $this->assertEquals('mysql',config('database.default'));
        $this->assertEquals('mysql',ConfigFacades::get('database.default'));
    }
}