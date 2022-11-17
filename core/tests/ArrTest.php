<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Support\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends testcase
{

    /** @test */
    public function it_can_get_item_from_array()
    {
        $arr = [
            'name' => 'ronald',
            'age' => 30,
            'address' => [
                'city' => 'guangzhou',
                'street' => [
                    'name' => 'xxx street',
                ]
            ],
        ];

        $this->assertEquals('ronald',Arr::get($arr,'name'));
        $this->assertEquals('default',Arr::get($arr,'not exist','default'));
        $this->assertNull(Arr::get($arr,'not exist'));

        $this->assertEquals('xxx street',Arr::get($arr,'address.street.name'));
        $this->assertEquals('guangzhou',Arr::get($arr,'address.city'));
    }
}