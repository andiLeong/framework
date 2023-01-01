<?php

namespace Andileong\Framework\Core\Jwt;

class JwtAuth
{
    public function __construct(private Jwt $jwt)
    {
        //
    }

    public function generate(string|int $user_id, int $expiredTime = null, array $payload = [])
    {
        return $this->jwt->generate(
            $this->payload($user_id,$expiredTime,$payload)
        );
    }

    private function payload(string|int $user_id, ?int $expiredTime, array $payload): array
    {
        return array_merge([
            'user_id' => $user_id,
            'expired_at' => $this->expiredAt($expiredTime),
        ],$payload);
    }

    private function expiredAt(?int $seconds = null): float|int
    {
        $seconds ??= $this->defaultExpiredTime();
        return time() + $seconds;
    }

    /**
     * get the default expired time stamp default is 3 hours
     * @return float|int
     */
    private function defaultExpiredTime(): float|int
    {
        return 60 * 60 * 3 ;
    }
}