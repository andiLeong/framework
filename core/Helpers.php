<?php


use Andileong\Framework\Core\Container\Container;

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
