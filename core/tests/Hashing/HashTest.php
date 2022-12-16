<?php

namespace Andileong\Framework\Tests\Hashing;

use Andileong\Framework\Core\Facades\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{

    /** @test */
    public function it_can_hash_using_the_default_driver()
    {
        $res = Hash::create('password');
        $this->assertTrue(Hash::verify('password',$res));
    }

    /** @test */
    public function it_can_hash_using_the_argon2i_driver()
    {
        $res = Hash::driver('argon2i')->create('password');
        $this->assertTrue(Hash::verify('password',$res));
    }
}
