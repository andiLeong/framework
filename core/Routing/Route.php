<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;

class Route
{
    public static $routes = [];
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

//        dump(self::$routes);
//        dd($path);
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
        if(!class_exists($controller)){
            throw new \Exception("Controller class not found {$controller}");
        }

        if(!method_exists($controller,$method)){
            $method = '__invoke';
        }

        $controllerInstance = $this->container->get($controller);
        $reflector = new \ReflectionMethod($controllerInstance,$method);
        $reflectionParameters = $reflector->getParameters();
        if(count($reflectionParameters) > 0) {

            $lists = [];
            foreach ($reflectionParameters as $param){
                if(!is_null($param->getType())){
                    $typeName = $param->getType()->getName();
                    $lists[] = $this->container->get($typeName);
                }else{
                    //has no type arguments maybe its from dynamic routing
                }
            }

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