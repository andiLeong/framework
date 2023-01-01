<?php

namespace Andileong\Framework\Tests\Jwt;

use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;
use Andileong\Framework\Core\Jwt\Header;
use Andileong\Framework\Core\Jwt\Jwt;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ValidateJwtTokenTest extends TestCase
{

    /** @test */
    public function it_throws_exception_if_token_is_not_a_jwt_token_format()
    {
        $this->expectException(JwtTokenValidationException::class);
        $jwt = new Jwt('secret', new Header());
        $token = 'fake-token';
        $jwt->validate($token);
    }

    /** @test */
    public function it_throws_exception_if_signature_is_not_valid()
    {
        $this->expectException(JwtTokenValidationException::class);
        $this->expectExceptionMessage('Invalid signature');
        $jwt = new Jwt('secret', new Header());
        $token = $jwt->generate([
            'user_id' => 99,
            'expired_at' => Carbon::now()->addHour()->timestamp,
        ]);
        $token = $token . 'ss';

        $jwt->validate($token);
    }
}
