<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * rendering controller/closure
     * @param $path
     * @param $method
     * @return mixed
     * @throws \Exception
     */
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

    /**
     * return whatever the controller/closure return to the frontend
     * @return JsonResponse|Response|void
     * @throws \Exception
     */
    public function run()
    {
        $content = $this->render();

        if (is_array($content)) {
            $response = new JsonResponse($content);
            return $response->send();
        }

        if ($content instanceof View) {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/html');
//            $response->setContent($content->getContent());

            return $response->send();
        }

        if (is_string($content)) {
            $response = new Response($content,);
            $response->headers->set('Content-Type', 'text/plain');

            return $response->send();
        }

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