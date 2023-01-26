<?php

namespace Andileong\Framework\Core\tests\Helper;

use Andileong\Framework\Core\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends testcase
{

    /** @test */
    public function it_can_separate_a_string()
    {
        $separator = '-';
        $uuid = '123e4567e89b12d3a456426614174000';
        $res1 = Str::separate($uuid, $separator, [8, 4, 4, 4, 12]);
        $res2 = Str::separate($uuid, $separator, [8, 4, 4, 4, 10]);
        $res3 = Str::separate($uuid, $separator, [8, 4, 4, 4, 13]);
        $res4 = Str::separate($uuid, '@', [8, 4, 4, 4, 13]);
        $res5 = Str::separate('abcdefg', '|');

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000',$res1);
        $this->assertEquals('123e4567-e89b-12d3-a456-4266141740-00',$res2);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000',$res3);
        $this->assertEquals('123e4567@e89b@12d3@a456@426614174000',$res4);
        $this->assertEquals('a|b|c|d|e|f|g',$res5);
    }

    /** @test */
    public function it_can_generate_a_uuid4()
    {
        $uuid = Str::uuid4();
        $this->assertTrue(Str::isUuid($uuid));
    }
}