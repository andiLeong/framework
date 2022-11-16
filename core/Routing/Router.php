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
        $this->routes['GET'][] = new Route($uri, $action, $this->container);
    }

    public function post($uri, $action)
    {
        $uri = $this->validateUri($uri);
        $this->routes['POST'][] = new Route($uri, $action, $this->container);
    }

    public function render($path = null, $method = null)
    {
        $method ??= $this->request->method();
        $path ??= $this->request->path();

        $routes = $this->routes[$method];
        $route = array_values(array_filter($routes, fn(Route $route) => $route->matches($path)
        ));

        if (count($route) === 0) {
            throw new \Exception('Route not found exception');
        }

        $route = $route[0];

        return $route->render();
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