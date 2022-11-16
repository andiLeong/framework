<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;

class Router
{
    public $routes = [];
    protected Request $request;
    private mixed $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request = $container[Request::class];
    }

    public function get($uri, $action)
    {
        $uri = $this->validateUri($uri);
        $this->routes['GET'][] = new Route($uri, 'GET', $action);
    }

    public function post($uri, $action)
    {
        $uri = $this->validateUri($uri);
        $this->routes['POST'][] = [
            'uri' => $uri,
            'action' => is_string($action) ? [$action] : $action,
        ];
    }

    public function render($path = null, $method = null)
    {
        $method ??= $this->request->method();
        $path ??= $this->request->path();

//        dump(self::$routes);
//        dd($path);
        $routes = $this->routes[$method];
        $route = array_values(array_filter($routes, fn(Route $route) => $route->matches($path)
        ));

        if (count($route) === 0) {
            throw new \Exception('Route not found exception');
        }

        $route = $route[0];

//        dd($route);
        if ($route->isClosure()) {
            return $route->callClosure();
        }

        $controller = $route->getController();
        $method = $route->getMethod();
        if (!class_exists($controller)) {
            throw new \Exception("Controller class not found {$controller}");
        }

        if (!method_exists($controller, $method)) {
            $method = '__invoke';
        }

        $controllerInstance = $this->container->get($controller);
        $reflector = new \ReflectionMethod($controllerInstance, $method);
        $reflectionParameters = $reflector->getParameters();
        if (count($reflectionParameters) > 0) {

//            dd($reflectionParameters);
//            dd($route);
            $lists = [];
            foreach ($reflectionParameters as $param) {
                $name = $param->name;

                $filteredParams = array_values(array_filter($route->getDynamicParams(), fn($param) => isset($param[$name])));

                if (count($filteredParams) > 0) {
                    $lists[] = $filteredParams[0][$name];
                    continue;
                }

                if (!is_null($param->getType())) {
                    $typeName = $param->getType()->getName();
                    if (class_exists($typeName)) {
                        $lists[] = $this->container->get($typeName);
                    }
                }
            }
//            dd($lists);

            return $controllerInstance->$method(...$lists);
        }
        return $controllerInstance->$method();
    }

    private function validateUri($uri)
    {
        $uri = rtrim($uri, '/');
        if (!str_starts_with($uri, '/')) {
            return '/' . $uri;
        }
        return $uri;
    }
}