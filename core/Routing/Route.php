<?php

namespace Andileong\Framework\Core\Routing;

use Andileong\Framework\Core\Support\Arr;
use App\Middleware\Middleware;

class Route
{
    protected $controller;
    protected $method;
    protected $isDynamic = false;
    protected $staticSegments = [];
    protected $dynamicParamNames = [];
    protected $dynamicParams = [];
    protected $middlewares = [];

    public function __construct(protected $uri, string|array|callable $action, public $container)
    {
        $this->parseAction($action);
        $this->parseDynamicRoute();
    }

    private function parseAction($action)
    {
        if ($action instanceof \Closure) {
            $this->controller = $action;
            return;
        }

        if (is_string($action)) {
            $action = [$action];
        }

        $this->controller = $action[0];
        $this->method = $action[1] ?? '__invoke';
    }

    /**
     * determine if the request path is match the uri
     * @param $path
     * @return bool|null
     */
    public function matches($path)
    {
        if ($path === $this->uri) {
            return true;
        }

        if ($this->isDynamic()) {
            return $this->matchesPattern($path);
        }

        return false;
    }

    protected function isClosure()
    {
        return $this->controller instanceof \Closure;
    }

    protected function callClosure()
    {
        return (new FunctionDispatcher($this))->dispatch();
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * parse the uri
     */
    private function parseDynamicRoute()
    {
        $pattern = "/{[a-z0-9A-Z_\-]+}/";
        if (preg_match_all($pattern, $this->uri, $matches)) {
            $this->makeStaticSegments($matches[0]);
            $this->saveDynamicParamName($matches[0]);
            $this->isDynamic = true;
        }
    }

    /**
     * check if the route is dynamic
     * @return bool|mixed
     */
    public function isDynamic()
    {
        return $this->isDynamic;
    }

    /**
     * make a regular expression for matching the dynamic route
     * @return string
     */
    protected function buildPattern()
    {
        $pattern = '';
        foreach ($this->staticSegments as $para) {
            $pattern .= "\/$para\/[0-9,a-z,A-z,\-,_]+";
        }

        return "/{$pattern}/";
    }

    /**
     * check the request path is matching the dynamic route
     * @param $path
     * @return bool
     */
    private function matchesPattern($path)
    {
        $pattern = $this->buildPattern();
        if (preg_match_all($pattern, $path, $matches)) {
            if ($matches[0][0] === $path) {
                $this->buildDynamicParams($path);
                return true;
            }
        }

        return false;
    }

    /**
     * extract any static params for dynamic route to build the regular expression later
     * eg /user/1/post/5 ,
     * use and post will be extracted
     * @param $matches
     */
    protected function makeStaticSegments($matches): void
    {
        $staticParams = array_reduce($matches, function ($ini, $item) {
            return str_replace($item, '', $ini);
        }, $this->uri);

        $staticParams = array_values(array_filter(explode('/', $staticParams)));

        $this->staticSegments = $staticParams;
    }

    public function getStaticSegments()
    {
        return $this->staticSegments;
    }

    public function getDynamicParams()
    {
        return $this->dynamicParams;
    }

    /**
     * save the parameter name eg /user/{id}
     * id will save to the array
     * @param $names
     */
    private function saveDynamicParamName($names)
    {
        $this->dynamicParamNames = array_map(function ($name) {
            return rtrim(ltrim($name, '{'), '}');
        }, $names);
    }

    /**
     * render the controller/closure
     * @return mixed
     * @throws \Exception
     */
    public function render()
    {
        if ($this->isClosure()) {
            return $this->callClosure();
        }

        return (new ControllerDispatcher($this))->dispatch();
    }

    /**
     * get all dynamic route params and save
     * eg user/1/post/50 1 and 50 will be saved
     * @param $matchedPath
     */
    protected function buildDynamicParams($matchedPath): void
    {
        $paramValues = array_values(
            $this->notInStaticParams($matchedPath)
        );

        foreach ($this->dynamicParamNames as $index => $name) {
            $this->dynamicParams[] = [$name => $paramValues[$index]];
        }
    }

    /**
     * @param $matchedPath
     * @return string[]
     */
    protected function notInStaticParams($matchedPath): array
    {
        return array_filter(explode('/', $matchedPath), function ($segment) {
            return !in_array($segment, $this->staticSegments, true) && $segment !== '';
        });
    }

    /**
     * attach middleware to the route
     * @param $middlewares
     * @return $this
     */
    public function middleware($middlewares)
    {
        $middlewares = is_array($middlewares) ? $middlewares : func_get_args();
        $this->middlewares = array_values(array_unique(array_merge($this->middlewares, $middlewares)));
        return $this;
    }

    /**
     * extract middlewares from registered app middleware lists
     * @return array
     */
    public function getMiddleware()
    {
        $filteredMiddlewares = [];
        foreach ($this->getRegisteredMiddleware() as $middleware) {
            if (array_key_exists($middleware, Middleware::$middlewares)) {
                $filteredMiddlewares[] = Middleware::$middlewares[$middleware];
            }
        }

        return $filteredMiddlewares;
    }

    public function getRegisteredMiddleware()
    {
        return $this->middlewares;
    }
}