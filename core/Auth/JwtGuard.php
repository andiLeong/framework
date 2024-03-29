<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Auth\Contracts\Guard;
use Andileong\Framework\Core\Hashing\HashManager;
use Andileong\Framework\Core\Request\Request;

class JwtGuard implements Guard
{
    use ValidateUserCredential;

    private $user;

    public function __construct(
        protected JwtAuth      $jwt,
        protected Request      $request,
        protected UserProvider $provider,
        protected HashManager  $hash,
    ) {
        //
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->request->retrieveBearerToken();
        $userId = $this->jwt->validate($token);
        return $this->user = $this->provider->retrievedById($userId);
    }

    /**
     * generate a jwt token
     * @param string|int $id
     * @param int|null $expiredTime
     * @param array $payload
     * @param string|null $algorithms
     * @return String
     */
    public function createJwtToken(string|int $id, int $expiredTime = null, array $payload = [], string $algorithms = null)
    {
        return $this->jwt->generate($id, $expiredTime, $payload, $algorithms);
    }
}
