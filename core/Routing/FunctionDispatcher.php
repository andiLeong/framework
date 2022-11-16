<?php

namespace Andileong\Framework\Core\Routing;

class FunctionDispatcher extends RouteDispatcher
{
    public function __construct(protected Route $route)
    {
        //
    }

    public function dispatch()
    {
        $closure = $this->route->getController();
        $reflector = new \ReflectionFunction($closure);
        $lists = $this->getParameterLists($reflector->getParameters());
        return call_user_func($closure,...$lists);
    }

    public function phaseParamException($name)
    {
        throw new \Exception("Unable to parse this parameter $name");
    }
}