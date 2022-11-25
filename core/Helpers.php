<?php

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\View\View;

if (!function_exists('resolveParam')) {

    /**
     * try to resolve a object out of an argument
     * @param ReflectionParameter $param
     * @param Application|Container|null $app
     * @return mixed|object|string|null
     */
    function resolveParam(ReflectionParameter $param, Application|Container $app = null)
    {
        if (is_null($param->getType())) {
            return;
        }

        $typeName = $param->getType()->getName();
        if (class_exists($typeName)) {
            $app ??= app();
            return $app->get($typeName);
        }
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

        return $app->get($key,$args);
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