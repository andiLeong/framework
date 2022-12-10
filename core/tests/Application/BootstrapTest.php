<?php

namespace Andileong\Framework\Tests\Application;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Bootstrap\Bootstrap;
use Andileong\Framework\Core\Bootstrap\HandleError;
use Andileong\Framework\Core\Bootstrap\LoadConfiguration;
use Andileong\Framework\Core\Bootstrap\SetEnvironmentVariable;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function set_env_is_called_when_booting_the_app()
    {
        $app = new Application($_SERVER['DOCUMENT_ROOT']);
        $setUpEnvMock = \Mockery::mock(SetEnvironmentVariable::class)->makePartial();
        $bootstrap = new Bootstrap($app);
        $bootstrap->bootstrapers = [
            $setUpEnvMock
        ];

        $setUpEnvMock->shouldReceive('bootstrap')->with($app)->once();
        $bootstrap->boot();
    }

    /** @test */
    public function handle_error_is_called_when_booting_the_app()
    {
        $app = new Application($_SERVER['DOCUMENT_ROOT']);
        $mock = \Mockery::mock(HandleError::class)->makePartial();
        $bootstrap = new Bootstrap($app);
        $bootstrap->bootstrapers = [
            $mock
        ];

        $mock->shouldReceive('bootstrap')->with($app)->once();
        $bootstrap->boot();
    }

    /** @test */
    public function set_config_is_called_when_booting_the_app()
    {
        $app = new Application($_SERVER['DOCUMENT_ROOT']);
        $mock = \Mockery::mock(LoadConfiguration::class)->makePartial();
        $bootstrap = new Bootstrap($app);
        $bootstrap->bootstrapers = [
            $mock
        ];

        $mock->shouldReceive('bootstrap')->with($app)->once();
        $bootstrap->boot();
    }
}
