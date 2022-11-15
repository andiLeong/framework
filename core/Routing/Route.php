<?php

namespace Andileong\Framework\Core\Routing;

class Route
{
    protected $controller;
    protected $method;
    protected $isDynamic = false;
    protected $staticSegments = [];
    protected $dynamicParamNames = [];
    protected $dynamicParams = [];

    public function __construct(protected $uri, protected $requestMethod, string|array|callable $action)
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

    public function isClosure()
    {
        return $this->controller instanceof \Closure;
    }

    public function callClosure()
    {
        return call_user_func($this->controller);
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    private function parseDynamicRoute()
    {
//        dd($this->uri);
        $pattern = "/{[a-z0-9A-Z_\-]+}/";
        if (preg_match_all($pattern, $this->uri, $matches)) {
//            dd($matches);


            $this->makeStaticSegments($matches[0]);
            $this->saveDynamicParamName($matches[0]);
//            dd($staticParams);
            $this->isDynamic = true;
        }

//        dd($this);
//        $this->makeStaticSegments($matches);
    }

    public function isDynamic()
    {
        return $this->isDynamic;
    }

    public function buildPattern()
    {
        $pattern = '';
        foreach ($this->staticSegments as $para) {
            $pattern .= "\/$para\/[1-9,a-z,A-z,\-,_]+";
        }

        return "/{$pattern}/";
    }

    private function matchesPattern($path)
    {
//        dd($this);
        $pattern = $this->buildPattern();
        if (preg_match_all($pattern, $path, $matches)) {
//            dump($this->staticSegments);
//            dump($this->dynamicParamNames);
//            dump($this->uri);
//            dump(array_values(
//                array_filter(explode('/',$matches[0][0]),function($segment){
//                    return !in_array($segment,$this->staticSegments,true) && $segment !== '';
//                }))
//            );
//            dump($matches[0][0]);

            if($matches[0][0] === $path){

                $paramValues = array_values(
                array_filter(explode('/',$matches[0][0]),function($segment){
                    return !in_array($segment,$this->staticSegments,true) && $segment !== '';
                }));
                foreach ($this->dynamicParamNames as $index => $name){
                    $this->dynamicParams[] = [$name => $paramValues[$index]];
                }
//                dd($this->dynamicParams);
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

    private function saveDynamicParamName($names)
    {
        $this->dynamicParamNames = array_map(function($name){
            return rtrim(ltrim($name, '{'),'}');
        },$names);
    }
}