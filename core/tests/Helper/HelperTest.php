<?php

namespace Andileong\Framework\Tests\Helper;

use Andileong\Framework\Core\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class HelperTest extends TestCase
{

    /** @test */
    public function it_can_get_a_json_response_instance()
    {
        $this->assertInstanceOf(JsonResponse::class, json(['hi' => 'there']));
    }

    /** @test */
    public function it_can_get_a_cache_driver_or_key()
    {
        $this->assertInstanceOf(CacheManager::class, cache());
        $this->assertEquals('default', cache('key-not-exist','default'));
    }

    /** @test */
    public function value_function_can_return_value_or_trigger_callback()
    {
        $this->assertEquals('value2', value('value2'));
        $fn = fn($value) => $value;
        $this->assertEquals('callback', value($fn,'callback'));
    }


}
