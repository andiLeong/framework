<?php

namespace Andileong\Framework\Tests\Jwt;

use Andileong\Framework\Core\Jwt\Algorithm;
use Andileong\Framework\Core\Jwt\Header;
use Andileong\Framework\Core\Jwt\Jwt;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{

    /** @test */
    public function it_can_generate_a_jwt_token()
    {
        $jwt = new Jwt('secret', new Header());
        $token = $jwt->generate([
            'user_id' => 99,
            'expired_at' => $expired = Carbon::now()->addHour()->timestamp,
        ]);

        $this->assertCount(3, explode('.', $token));
    }

    /** @test */
    public function it_can_generate_a_jwt_token_with_hs384()
    {
        $jwt = new Jwt('secret', new Header());
        $token = $jwt->generate([
            'user_id' => 99,
            'expired_at' => $expired = Carbon::now()->addHour()->timestamp,
        ],'hs384');

        $this->assertCount(3, explode('.', $token));
    }

    /** @test */
    public function it_can_get_a_match_algorithm()
    {
        $hs256 = Algorithm::from('hs256')->value;
        $this->assertEquals($hs256,'hs256');

        $this->expectException(\ValueError::class);
        Algorithm::from('aaaa')->value;
    }
}
