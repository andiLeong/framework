<?php

namespace Andileong\Framework\Core\tests\Cache;

use Andileong\Framework\Core\Cache\FileCacheHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class CacheFileStoreTest extends TestCase
{
    private FileCacheHandler $cache;

    public function setUp(): void
    {
        $this->cache = new FileCacheHandler(app());
        $this->cache->remove();
    }

    public function tearDown(): void
    {
        $this->cache->remove();
    }

    /** @test */
    public function it_can_store_a_cache_item()
    {
        $res = $this->cache->put('foo', 'bar', 10);
        $file = $this->cache->getDirectory() . '/' . md5('foo');
        $this->assertTrue(file_exists($file));
        $this->assertTrue($res);
    }

    /** @test */
    public function it_can_get_item_from_cache()
    {
        $this->cache->put('foo', 'bar');
        $this->assertEquals('bar', $this->cache->get('foo'));
        $this->assertEquals('default', $this->cache->get('bar', 'default'));
    }

    /** @test */
    public function when_get_expired_item_file_will_be_deleted()
    {
        $this->cache->put('abc', 'efg');
        $file = $this->cache->getDirectory() . '/' . md5('foo');

        $this->cache->setKeyToExpired('abc');
        $res = $this->cache->get('abc');
        $this->assertFalse(file_exists($file));
        $this->assertNull($res);
    }

    /** @test */
    public function it_can_remove_all_cache_items()
    {
        $this->cache->put('abc', 'efg');
        $directory = $this->cache->getDirectory();
        $this->cache->remove();

        $finder = new Finder();
        $finder->files()->in($directory);
        $this->assertCount(0, $finder);
    }

    /** @test */
    public function it_can_delete_a_single_cache_items()
    {
        $this->cache->put('abc', 'efg');
        $this->cache->put('efg', 'xyz');
        $directory = $this->cache->getDirectory();
        $this->cache->delete('abc');

        $finder = new Finder();
        $finder->files()->in($directory);
        $this->assertCount(1, $finder);
    }

    /** @test */
    public function it_can_store_multiple_items_at_a_same_time()
    {
        $this->cache->putMany([
            'foo' => 'bar',
            'faz' => 'baz',
        ]);

        $foo = $this->cache->get('foo');
        $faz = $this->cache->get('faz');
        $this->assertEquals('bar', $foo);
        $this->assertEquals('baz', $faz);
    }

    /** @test */
    public function it_can_check_an_item_exist()
    {
        $res = $this->cache->has('foo');
        $this->cache->put('key','value');
        $this->cache->put('abc','value33');

        $res3 = $this->cache->has('abc');
        $this->assertTrue($res3);

        $this->cache->setKeyToExpired('abc');
        $res3 = $this->cache->has('abc');
        $res2 = $this->cache->has('key');

        $this->assertFalse($res);
        $this->assertFalse($res3);
        $this->assertTrue($res2);
    }
}
