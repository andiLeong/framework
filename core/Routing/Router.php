<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Exception\ExceptionHandler;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\View\View;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    public $routes = [];
    protected Request $request;

    public function __construct(private Container $container)
    {
//        $container ??= app();
//        $this->container = $container;
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

    public function getContentFromRender($path = null, $method = null)
    {
        try {
            $content = $this->render($path, $method);
        } catch (Exception $e) {
            $handler = app('exception.handler',[$e]);
            return $handler->handle();
        }

        return $content;
    }

    /**
     * return whatever the controller/closure return to the frontend
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function run($path = null, $method = null)
    {
        $content = $this->getContentFromRender($path, $method);

        if (is_array($content) || $content instanceof \JsonSerializable) {
            $response = new JsonResponse($content);
            return $response->send();
        }

        if ($content instanceof View) {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/html');
            return $response->send();
        }

        if ($content instanceof Response) {
            return $content->send();
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain');
        return $response->send();
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