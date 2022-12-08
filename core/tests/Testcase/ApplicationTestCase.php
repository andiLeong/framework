<?php

namespace Andileong\Framework\Core\tests\Testcase;

use Andileong\Framework\Core\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class ApplicationTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
    use Http;

    protected Application $app;

    public function setUp(): void
    {
        $this->createApp();
    }

    public function createApp()
    {
        $this->app = new Application($_SERVER['DOCUMENT_ROOT']);
    }

    public function fake($key, $concrete)
    {
        $this->app->setSingleton($key, $concrete);
        return $this;
    }
}