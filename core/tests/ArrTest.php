<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Support\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends testcase
{
    public $arr = [
        'name' => 'ronald',
        'age' => 30,
        'address' => [
            'city' => 'guangzhou',
            'detail' => [
                'country' => [
                    'name' => 'American'
                ]
            ],
            'street' => [
                'name' => 'xxx street',
            ]
        ],
    ];

    /** @test */
    public function it_can_get_item_from_array()
    {
        $this->assertEquals('ronald', Arr::get($this->arr, 'name'));
        $this->assertEquals('default', Arr::get($this->arr, 'not exist', 'default'));
        $this->assertNull(Arr::get($this->arr, 'not exist'));

        $this->assertEquals('xxx street', Arr::get($this->arr, 'address.street.name'));
        $this->assertEquals('guangzhou', Arr::get($this->arr, 'address.city'));
    }

    /** @test */
    public function it_can_get_certain_items_from_array()
    {
        $this->assertEquals(['name' => 'ronald'], Arr::only($this->arr, 'name'));
        $this->assertEquals(['name' => 'ronald', 'age' => 30], Arr::only($this->arr, ['name', 'age', 'no']));
        $this->assertEquals(['age' => 30, 'name' => 'ronald'], Arr::only($this->arr, ['name', 'age', 'no']));
    }

    /** @test */
    public function it_can_get_first_item_from_array()
    {
        $arr = [3, 5, 10, 70, 32];
        $this->assertEquals(3, Arr::first($arr));
        $this->assertEquals('ronald', Arr::first($this->arr));
        $this->assertnull(Arr::first([]));
        $this->assertEquals(10, Arr::first($arr, fn($ar) => $ar === 10));
        $this->assertEquals(999, Arr::first($arr, fn($ar) => $ar === 999, 999));
    }

    /** @test */
    public function it_can_get_last_item_from_array()
    {
        $arr = [3, 5, 10, 70, 32];
        $this->assertEquals(32, Arr::last($arr));
        $this->assertEquals(32, Arr::last($arr, fn($ar) => $ar >= 10));
        $this->assertEquals(999, Arr::last($arr, fn($ar) => $ar === 999, 999));
    }

    /** @test */
    public function it_can_check_if_item_exists()
    {
        $this->assertTrue(Arr::has($this->arr, 'name'));
        $this->assertFalse(Arr::has($this->arr, 'no'));
        $this->assertTrue(Arr::has($this->arr, 'address.city'));
        $this->assertTrue(Arr::has($this->arr, 'address.detail.country.name'));
        $this->assertTrue(Arr::has($this->arr, 'address.detail.country'));
        $this->assertFalse(Arr::has($this->arr, 'address.city2'));
    }
}