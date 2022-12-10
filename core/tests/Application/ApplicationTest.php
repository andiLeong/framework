<?php

namespace Andileong\Framework\Tests\Application;

use Andileong\Framework\Core\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends testCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function boot_method_is_call_when_instantiate_application()
    {
        $mock = \Mockery::mock(Application::class)->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('boot')->once();
        $mock->__construct($_SERVER['DOCUMENT_ROOT']);
    }

    /** @test */
    public function load_alias_method_is_call_when_instantiate_application()
    {
        $mock = \Mockery::mock(Application::class)->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('loadAlias')->once();
        $mock->__construct($_SERVER['DOCUMENT_ROOT']);
    }

    /** @test */
    public function register_binding_method_is_call_when_instantiate_application()
    {
        $this->markTestSkipped();
        $mock = \Mockery::mock(Application::class)->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('registerBinding')->once();
        $mock->__construct($_SERVER['DOCUMENT_ROOT']);
    }
}
