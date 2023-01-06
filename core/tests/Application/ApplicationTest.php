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
        $mock->shouldReceive('loadAlias')->once();
        $mock->shouldReceive('registerBinding')->once();
        $mock->shouldReceive('boot')->once();
        $mock->shouldReceive('loadServices')->once();
        $mock->__construct($_SERVER['DOCUMENT_ROOT']);
    }

}
