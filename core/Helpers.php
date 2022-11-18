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
     * get an app from app array
     * @param null $key
     * @return object|null
     */
    function app($key = null)
    {
        $app = Container::getInstance();
        if(is_null($key)){
            return $app;
        }

        return $app->get($key);
    }

}