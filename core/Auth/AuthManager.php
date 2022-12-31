<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Support\Traits\HasMultipleDrivers;

/**
 * @method attempt(array $credential)
 * @method user()
 */
class AuthManager
{
    use HasMultipleDrivers;

    private mixed $config;

    public function __construct(protected Application $app)
    {
        $this->config = $app->get('config');
    }

    public function createTokenDriver()
    {
        return new TokenGuard(
            $this->app->get('request'),
            new UserProvider(
                $this->config['auth.drivers.token.provider'],
            ),
            $this->app->get('hash')
        );
    }

    public function getDefaultDriverName()
    {
        return $this->config['auth.default'];
    }
}