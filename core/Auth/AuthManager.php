<?php

namespace Andileong\Framework\Core\Auth;

use Andileong\Collection\Collection;
use Andileong\Framework\Core\Application;
use Exception;
use InvalidArgumentException;

/**
 * @method attempt(array $credential)
 * @method user()
 */
class AuthManager
{

    public $instances = [];
    private mixed $authConfig;

    public function __construct(protected Application $app)
    {
        $this->authConfig = $app->get('config')->get('auth');
    }

    /**
     * call a guard's driver
     * @param $guard
     * @return mixed
     * @throws Exception
     */
    public function guard($guard = null)
    {
        [$name, $guard] = $this->getGuard($guard);
        $driver = $guard['driver'];

        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }

        $method = 'create' . ucfirst($driver) . 'Driver';
        if (!method_exists($this, $method)) {
            throw new Exception('driver ' . $driver . ' not found');
        }

        return $this->instances[$name] = $this->{$method}($name);
    }


    /**
     * create a token base driver
     * @param $guard
     * @return TokenGuard
     * @throws Exception
     */
    public function createTokenDriver($guard)
    {
        $providerConfig = $this->authConfig['guards'][$guard]['provider'];
        return new TokenGuard(
            $this->app->get('request'),
            new UserProvider(
                $providerConfig
            ),
            $this->app->get('hash')
        );
    }

    /**
     * create a jwt base driver
     * @param $guard
     * @return JwtGuard
     * @throws Exception
     */
    public function createJwtDriver($guard)
    {
        $providerConfig = $this->authConfig['guards'][$guard]['provider'];
        return new JwtGuard(
            $this->app->get('jwt.auth',[$guard]),
            $this->app->get('request'),
            new UserProvider(
                $providerConfig
            ),
            $this->app->get('hash')
        );
    }

    /**
     * get a default guard
     * @return mixed
     */
    protected function getDefaultGuardName()
    {
        return $this->authConfig['default'];
    }

    /**
     * get the guard out of the config
     * @param $guard
     * @return array
     */
    protected function getGuard($guard = null)
    {
        if (is_null($guard)) {
            $guard = $this->getDefaultGuardName();
        }

        $guards = Collection::make($this->authConfig['guards'])->filter(
            fn($value, $key) => $key === $guard
        );

        if ($guards->isEmpty()) {
            throw new InvalidArgumentException('auth guard is not found in your auth config ' . $guard);
        }

        return [
            $guard,
            $guards->get($guard)
        ];
    }

    /**
     * handle dynamic calling to driver
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        return [$this->guard(), $name](...$arguments);
    }

}