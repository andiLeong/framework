<?php

namespace Andileong\Framework\Tests\Jwt;

use Andileong\Framework\Core\Jwt\Algorithm;
use Andileong\Framework\Core\Jwt\Header;
use Andileong\Framework\Core\Jwt\Jwt;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{
    private Jwt $jwt;
    private $payload = [
        'user_id' => 99,
    ];

    public function setUp() :void
    {
        $this->jwt = new Jwt('secret', new Header());
    }

    /** @test */
    public function it_can_generate_a_jwt_token()
    {
        $token = $this->jwt->generate($this->payload + ['expire_at' => time() + 2000]);
        $token2 = $this->jwt->generate($this->payload + ['expire_at' => time() + 2001]);
        dump($token);
        dump($token2);

        $this->assertPayloadSame($token);
    }

    /** @test */
    public function it_can_generate_a_jwt_token_with_hs384()
    {
        $token = $this->jwt->generate($this->payload, 'hs384');
        $this->assertPayloadSame($token);
    }

    /** @test */
    public function it_can_generate_a_jwt_token_with_hs512()
    {
        $token = $this->jwt->generate($this->payload, 'hs512');
        $this->assertPayloadSame($token);
    }

    public function assertPayloadSame($token)
    {
        $payload = $this->jwt->validate($token);
        $this->assertEquals(99, $payload['user_id']);
    }

    /** @test */
    public function it_can_get_a_match_algorithm()
    {
        $hs256 = Algorithm::from('hs256')->value;
        $this->assertEquals($hs256, 'hs256');

        $this->expectException(\ValueError::class);
        Algorithm::from('aaaa')->value;
    }
}
