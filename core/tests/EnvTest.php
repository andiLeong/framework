<?php

namespace Andileong\Framework\Tests;

use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{

    /** @test */
    public function it_can_env_variable_by_global_helper()
    {
        $this->assertEquals('file',env('CACHE_DRIVER'));
    }
}
