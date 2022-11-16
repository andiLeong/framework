<?php


use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\View\View;

if (!function_exists('resolveParam')) {

    /**
     * try to resolve a object out of an argument
     * @param ReflectionParameter $param
     * @param Container|null $container
     * @return mixed|object|string|null
     * @throws Exception
     */
    function resolveParam(ReflectionParameter $param, Container $container = null)
    {
        if (is_null($param->getType())) {
            return;
        }

        $typeName = $param->getType()->getName();
        if (class_exists($typeName)) {
            $container ??= new Container();
            return $container->get($typeName);
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