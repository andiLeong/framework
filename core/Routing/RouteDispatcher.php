<?php

namespace Andileong\Framework\Core\Routing;

abstract class RouteDispatcher
{
    /**
     * try to get the method params from the route params array if possible
     * @param $name
     * @return array|mixed
     */
    private function getFromRouteParams($name): mixed
    {
        return array_filter($this->route->getDynamicParams(), fn ($param) => isset($param[$name]));
    }

    public function getParameterLists($parameters)
    {
        return array_map(function ($param) {
            $name = $param->name;
            $filteredParams = array_values($this->getFromRouteParams($name));

            if (count($filteredParams) > 0) {
                return $filteredParams[0][$name];
            }

            $resolved = resolveParam($param, $this->route->container);
            if ($resolved) {
                return $resolved;
            }

            $this->phaseParamException($param);
        }, $parameters);
    }

    abstract public function phaseParamException($name);
}
