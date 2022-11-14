<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;

class Route
{
    public static $routes = [];
    protected Request $request;

    public function __construct(Container $container)
    {
        $this->request = $container['request'];
    }

    public function get($uri, $action)
    {
        $uri = $this->validateUri($uri);
        self::$routes['GET'][] = [
            'uri' => $uri,
            'action' => $action,
        ];
    }

    public function post($uri, $action)
    {
        $uri = $this->validateUri($uri);
        self::$routes['POST'][] = [
            'uri' => $uri,
            'action' => $action,
        ];
    }

    public function run()
    {
        $method = $this->request->method();
        $path = $this->request->path();

        dump(self::$routes);
        dd($path);
        $routes = self::$routes[$method];
        $route = array_values(array_filter($routes, function ($route) use ($path) {
            return $route['uri'] === $path;
        }));

        if (count($route) === 0) {
            throw new \Exception('Route not found exception');
        }

//        dd($route);
        if (is_callable($route[0]['action'])) {
            return call_user_func($route[0]['action']);
        }

        $controller = $route[0]['action'][0];
        $method = $route[0]['action'][1];
        $toCall = [
            new $controller,
            $method
        ];
        return $toCall();
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