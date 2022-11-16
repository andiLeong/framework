<?php

namespace Andileong\Framework\Core\Routing;

class ControllerDispatcher extends RouteDispatcher
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
        $lists = $this->getParameterLists($reflector->getParameters());

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

    public function phaseParamException($name)
    {
        throw new \Exception("Unable to parse this parameter $name for this controller {$this->route->getController()}");
    }
}