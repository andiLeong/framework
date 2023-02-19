<?php

namespace Andileong\Framework\Tests\Helper;

use Andileong\Framework\Core\Support\Benchmark;
use Andileong\Framework\Core\Support\Str;
use PHPUnit\Framework\TestCase;

class BenchmarkTest extends TestCase
{
    /** @test */
    public function it_can_benchmark_array_of_functions()
    {
        $array = [];
        foreach (range(1, 1000) as $value) {
            $array[] = Str::random(64);
        }

        $res = Benchmark::start([
            'filter' => fn() => array_filter($array, fn($v) => str_starts_with($v, 'a')),
            'foreach' => function () use ($array) {
                $res = [];
                foreach ($array as $v) {
                    if (str_starts_with($v, 'a')) {
                        $res[] = $v;
                    }
                }
                return $res;
            }
        ]);

        $filterTime = floatval(Str::before($res['filter'], 'ms'));
        $foreachTime = floatval(Str::before($res['filter'], 'ms'));

        $this->assertArrayHasKey('filter', $res);
        $this->assertArrayHasKey('foreach', $res);
        $this->assertTrue(str_contains($res['filter'], 'ms'));
        $this->assertTrue(str_contains($res['foreach'], 'ms'));
        $this->assertIsFloat($filterTime);
        $this->assertIsFloat($foreachTime);
    }
}
