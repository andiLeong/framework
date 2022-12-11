<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

if (!function_exists('resolveParam')) {

    /**
     * try to resolve a object out of an argument
     * @param ReflectionParameter $param
     * @param Application|Container|null $app
     * @return mixed|object|string|null
     * @throws Exception
     */
    function resolveParam(ReflectionParameter $param, Application|Container $app = null)
    {
        if (is_null($param->getType())) {
            return;
        }

        $typeName = $param->getType()->getName();
        return $app->get($typeName);
    }

}

if (!function_exists('view')) {

    /**
     * render a view
     * @param $path
     * @param array $data
     * @return object|null
     */
    function view($path, $data = [])
    {
        $view = new View();
        return $view->render($path, $data);
    }

}

if (!function_exists('env')) {

    /**
     * get an env from env array
     * @param $key
     * @param null $default
     * @return object|null
     */
    function env($key, $default = null)
    {
        return Arr::get($_ENV, $key, $default);
    }

}

if (!function_exists('app')) {

    /**
     * get global app instance or resolve it
     * @param null $key
     * @return object|null
     */
    function app($key = null, $args = [])
    {
        $app = Container::getInstance();
        if (is_null($key)) {
            return $app;
        }

        return $app->get($key, $args);
    }

}


if (!function_exists('config')) {

    /**
     * get an config item from container
     * @param null $key
     * @return object|null
     */
    function config($key = null, $default = null)
    {
        if ($key === null) {
            return app('config');
        }
        return app('config')->get($key, $default);
    }

}

if (!function_exists('request')) {

    /**
     * resolve request singleton from container
     * @param null $key
     * @return object|null
     */
    function request($key = null, $default = null)
    {
        $request = app('request');
        if ($key === null) {
            return $request;
        }

        return $request->get($key, $default);
    }

}

if (!function_exists('resolve')) {
    /**
     * resolve a thing from the container
     * @param null $key
     * @return object|null
     */
    function resolve($key)
    {
        return app($key);
    }
}

if (!function_exists('appPath')) {
    /**
     * retrieve document root
     * @return object|null
     */
    function appPath()
    {
        return app('app_path');
    }
}

if (!function_exists('storagePath')) {
    /**
     * retrieve storage path
     * @return object|null
     */
    function storagePath()
    {
        return app('storage_path');
    }
}

if (!function_exists('classBaseName')) {
    /**
     * get classBaseName
     * @param $class
     * @return string
     * @throws ReflectionException
     */
    function classBaseName($class)
    {
        $reflector = new ReflectionClass($class);
        return $reflector->getShortName();
    }
}

if (!function_exists('hasMethodDefined')) {
    /**
     * check an object have a custom method defined
     * @string|object $class
     * @return string
     */
    function hasMethodDefined($class, $method, $default = null)
    {
        $object = is_object($class)
            ? $class
            : app($class);

        if (method_exists($object, $method)) {
            return $method;
        }
        return $default;
    }
}

if (!function_exists('logger')) {
    /**
     * quickly to log information
     * @param $message
     * @param string $level
     * @param array $context
     * @return void
     */
    function logger($message, $level = 'error', $context = [])
    {
        app('logger')->{$level}($message, $context);
    }
}

if (!function_exists('ensureDirectoryExisted')) {
    /**
     * quickly to log information
     * @param $path
     * @param string $permission
     * @return void
     */
    function ensureDirectoryExisted($path, $permission = '0775')
    {
        if (!file_exists($path)) {
            mkdir($path, $permission, true);
        }
    }
}

if (!function_exists('value')) {
    /**
     * retrieve a value if not callable , else call it and return
     * @param $value
     * @param array $params
     * @return void
     */
    function value($value, ...$params)
    {
        return is_callable($value) ? $value(...$params) : $value;
    }
}

if (!function_exists('cache')) {
    /**
     * get the cache instance or get the key from the cache
     * @param null|string $key
     * @param null|closure $default
     * @return object
     */
    function cache($key = null, $default = null)
    {
        $cache = app('cache');
        if (is_null($key)) {
            return $cache;
        }

        return $cache->get($key, $default);
    }
}

if (!function_exists('json')) {
    /**
     * get a Symfony json response object instance
     * @param $body
     * @param int $code
     * @param array $headers
     * @return object
     */
    function json($body, $code = 200, $headers = [])
    {
        return new JsonResponse($body, $code, $headers);
    }
}