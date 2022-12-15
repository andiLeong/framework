<?php

namespace Andileong\Framework\Tests\Cache;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Cache\RedisCacheHandler;
use Andileong\Framework\Core\Database\Connection\RedisConnection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class RedisStoreTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_can_put_things_to_redis_cache_driver()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('set')->with('cache_foo', serialize('bar'), 'EX', 60)->once();

        $redis = $this->getRedisStore($mock);
        $redis->put('foo','bar',60);
    }

    /** @test */
    public function it_can_put_things_to_redis_cache_driver_forever()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('set')->with('cache_foo', serialize('bar'))->once();

        $redis = $this->getRedisStore($mock);
        $redis->put('foo','bar');
    }

    /** @test */
    public function it_can_put_things_to_redis_cache_driver_forever_using_forever()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('set')->with('cache_foo', serialize('bar'))->once();

        $redis = $this->getRedisStore($mock);
        $redis->forever('foo','bar');
    }

    /** @test */
    public function it_can_determine_a_key_exists_in_redis_store()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('get')->with('cache_foo')->andReturn(null)->once();

        $redis = $this->getRedisStore($mock);
        $this->assertFalse($redis->has('foo'));

        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('get')->with('cache_foo')->andReturn(serialize('bar'))->once();

        $redis = $this->getRedisStore($mock);
        $this->assertTrue($redis->has('foo'));
    }

    /** @test */
    public function it_can_delete_a_redis_cache_key()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('del')->with('cache_foo')->andReturn(true)->once();

        $redis = $this->getRedisStore($mock);
        $redis->delete('foo');
    }

    /** @test */
    public function it_can_get_a_redis_cache_key()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('get')->with('cache_foo')->andReturn(null)->once();

        $redis = $this->getRedisStore($mock);
        $redis->get('foo');
    }

    /** @test */
    public function it_can_remove_all_redis_cache_keys()
    {
        $mock = \Mockery::mock(RedisConnection::class);
        $mock->shouldReceive('getRedis')->andReturn($mock)->once();
        $mock->shouldReceive('del')->with('cache_foo')->andReturn(true)->once();
        $mock->shouldReceive('keys')->with('*')->andReturn([
            'cache_foo',
            'redis_key'
        ])->once();

        $redis = $this->getRedisStore($mock);
        $redis->remove();
    }

    public function getRedisStore($mock)
    {
        $app = new Application($_SERVER['DOCUMENT_ROOT']);
        $app->setSingleton('redis', $mock);
        return new RedisCacheHandler($app);
    }
}
