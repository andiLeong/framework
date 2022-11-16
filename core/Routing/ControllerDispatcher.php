<?php

namespace Andileong\Framework\Core\Routing;

class ControllerDispatcher
{

    private $instance;
    private $method;

    public function __construct(
        protected Route $route,
    )
    {
        $this->parseController();
    }

    public function dispatch()
    {
        $reflector = new \ReflectionMethod($this->instance, $this->method);
        $reflectionParameters = $reflector->getParameters();

        $lists = array_map(function ($param) {

            $name = $param->name;
            $filteredParams = array_values($this->getFromRouteParams($name));

            if (count($filteredParams) > 0) {
                return $filteredParams[0][$name];
            }

            if (!is_null($param->getType())) {
                $typeName = $param->getType()->getName();
                if (class_exists($typeName)) {
                    return $this->route->container->get($typeName);
                }
            }

            throw new \Exception("Unable to parse this parameter $name for this controller {$this->route->getController()}");

        }, $reflectionParameters);

        return [
            $this->instance,
            $this->method
        ](...$lists);
    }

    private function parseController(): void
    {
        $controller = $this->route->getController();
        $method = $this->route->getMethod();

        if (!method_exists($controller, $method)) {
            $method = '__invoke';
        }

        $this->instance = $this->route->container->get($controller);
        $this->method = $method;
    }

    /**
     * try to get the method params from the route params if possible
     * @param $name
     * @return array|mixed
     */
    function getFromRouteParams($name): mixed
    {
        return array_filter($this->route->getDynamicParams(), fn($param) => isset($param[$name]));
    }
}