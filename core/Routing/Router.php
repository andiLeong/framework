<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Container\Container;
use Andileong\Framework\Core\Pipeline\Pipeline;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Response\Pipes\AddCors;
use Andileong\Framework\Core\Response\Pipes\PersistCookie;
use Andileong\Framework\Core\Response\Pipes\SaveSession;
use Andileong\Framework\Core\View\View;
use App\Middleware\Middleware;
use Closure;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Router
{
    public $routes = [];
    public $uri = [];
    public $middlewares = [];
    public Request $request;
    private $onGroup = false;

    public function __construct(private Container $container)
    {
        $this->request = $container['request'];
    }

    /**
     * register a get uri endpoint
     * @param $uri
     * @param $action
     * @return route
     */
    public function get($uri, $action): route
    {
        return $this->register('get', $uri, $action);
    }

    /**
     * register a post uri endpoint
     * @param $uri
     * @param $action
     * @return Route
     */
    public function post($uri, $action)
    {
        return $this->register('post', $uri, $action);
    }

    /**
     * register a delete uri endpoint
     * @param $uri
     * @param $action
     * @return Route
     */
    public function delete($uri, $action)
    {
        return $this->register('delete', $uri, $action);
    }

    /**
     * register a put uri endpoint
     * @param $uri
     * @param $action
     * @return Route
     */
    public function put($uri, $action)
    {
        return $this->register('put', $uri, $action);
    }

    /**
     * register a patch uri endpoint
     * @param $uri
     * @param $action
     * @return Route
     */
    public function patch($uri, $action)
    {
        return $this->register('patch', $uri, $action);
    }

    /**
     * register any given method route
     * @param $method
     * @param $uri
     * @param $action
     * @return Route
     */
    protected function register($method, $uri, $action)
    {
        $uri = $this->validateUri($uri);
        $route = new Route($uri, $action, $this->container);
        $this->addMiddlewareToRoute($route);
        $this->routes[strtoupper($method)][] = $route;

        return $route;
    }

    /**
     * rendering controller/closure
     * @return mixed
     * @throws Exception
     */
    public function render()
    {
        return $this->runGlobalMiddlewares(fn (Request $request) => $this->findRoute($request));
    }


    /**
     * try to find a matching route and dispatch the route
     * @param Request $request
     * @return mixed
     * @throws RouteNotFoundException
     */
    protected function findRoute(Request $request)
    {
        //here the request may be mutated since it was filtered by middleware
        //therefor we need to update the container request and local request property
        $this->request = $request;
        $this->container->setSingleton('request', $request);

        $method = $request->method();
        $path = $request->path();

        if (!isset($this->routes[$method])) {
            throw new RouteNotFoundException($path . ' Route not found exception, No verb registered ' . $method, 404);
        }

        $routes = $this->routes[$method];
        $route = array_values(array_filter(
            $routes,
            fn (Route $route) => $route->matches($path)
        ));

        if (!count($route)) {
            throw new RouteNotFoundException('Route not found exception', 404);
        }

        $route = $route[0];

        return $this->runThroughMiddleware($route, $request);
    }

    /**
     * run the global middlewares
     * @param callable $next
     * @return mixed
     * @throws Exception
     */
    private function runGlobalMiddlewares(callable $next)
    {
        $this->container->setSingleton('request', $this->request);
        $pipeline = $this->container->get(Pipeline::class);
        return $pipeline
            ->send($this->request)
            ->through($this->getGlobalMiddlewares())
            ->run()
            ->then($next);
    }

    /**
     * run the route with middlewares if any
     * @param Route $route
     * @param null $request
     * @return mixed
     * @throws Exception
     */
    private function runThroughMiddleware(Route $route, $request = null)
    {
        $request ??= $this->request;
        $pipeline = $this->container->get(Pipeline::class);
        return $pipeline
            ->send($request)
            ->through($route->getMiddleware())
            ->run()
            ->then(fn ($request) => $route->render());
    }

    /**
     * fetch the global run middlewares
     * @return mixed
     * @throws Exception
     */
    protected function getGlobalMiddlewares()
    {
        return $this->container->get(Middleware::class)->golbalMiddlewares;
    }

    /**
     * try to get content from route throw exception if encountered
     * @return mixed
     * @throws Exception
     */
    public function getContentFromRender()
    {
        try {
            $content = $this->render();
        } catch (Throwable $e) {
            $handler = $this->container->get('exception.handler', [$e]);
            $this->container->get('logger')->error($e->getTraceAsString());
            return $handler->handle();
        }

        return $content;
    }

    /**
     * return whatever the controller/closure return to the frontend
     * @return Response
     * @throws Exception
     */
    public function run(): Response
    {
        $content = $this->getContentFromRender();

        if (is_array($content) || $content instanceof \JsonSerializable) {
            return new JsonResponse($content);
        }

        if ($content instanceof View) {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        if ($content instanceof Response) {
            return $content;
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    /**
     * send Symfony response to client
     * @return Response
     * @throws Exception
     */
    public function response()
    {
        $response = $this->container->get(Pipeline::class)
            ->send($this->run())
            ->through([
                AddCors::class,
                SaveSession::class,
                PersistCookie::class,
            ])
            ->run()
            ->result();

        return $response->send();
    }

    /**
     * validate uri before save to memory
     * @param $uri
     * @return string
     */
    public static function validateUri($uri)
    {
        $uri = rtrim($uri, '/');
        if (!str_starts_with($uri, '/')) {
            return '/' . $uri;
        }
        return $uri;
    }

    /**
     * save the registered middlewares
     * @param $middlewares
     * @return $this
     */
    public function middleware($middlewares)
    {
        $this->middlewares = (is_array($middlewares) ? $middlewares : func_get_args());
        return $this;
    }

    /**
     * group closure of routes to middleware
     * @param Closure $closure
     */
    public function group(Closure $closure)
    {
        $this->onGroup = true;
        $closure($this);
        if ($this->hasMiddlewareRegistered()) {
            $this->middlewares = [];
        }

        $this->onGroup = false;
    }

    /**
     * check if has register any middlewares
     * @return bool
     */
    private function hasMiddlewareRegistered()
    {
        return !empty($this->middlewares);
    }

    /**
     * add the registered middleware to a route
     * @param Route $route
     */
    private function addMiddlewareToRoute(Route $route)
    {
        if ($this->hasMiddlewareRegistered()) {
            $route->middleware($this->middlewares);
            if (!$this->onGroup) {
                $this->middlewares = [];
            }
        }
    }

    /**
     * set request to current
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }
}
