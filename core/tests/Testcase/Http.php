<?php

namespace Andileong\Framework\Core\tests\Testcase;

use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;

trait Http
{
    public function put($uri, $data = [], $headers = [])
    {
        return $this->call('put', $uri, $data, $headers);
    }

    public function delete($uri, $data = [], $headers = [])
    {
        return $this->call('delete', $uri, $data, $headers);
    }

    public function post($uri, $data = [], $headers = [])
    {
        return $this->call('post', $uri, $data, $headers);
    }

    public function get($uri, $data = [])
    {
        return $this->call('get', $uri, $data);
    }

    public function call($method, $uri, $data = [], $headers = [])
    {
        [$method, $uri, $query, $payload] = $this->getRequestParams($method, $uri, $data);
        $request = Request::setTest($query, $payload, [
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => $method,
            'HTTP_HOST' => 'localhost:8080',
        ], $headers);

        $this->app->setSingleton('request', $request);

        require './routes/routes.php';

        $router = $this->app['router'];
        $router->setRequest($request);
        return new Response($this, $router->run());
    }

    protected function getRequestParams(mixed $method, string $uri, mixed $data)
    {
        $method = strtoupper($method);
        $uri = Router::validateUri($uri);

        if ($method === 'GET') {
            $payload = [];
            $query = $data;
        } else {
            $query = [];
            $payload = $data;
        }

        return [$method, $uri, $query, $payload];
    }
}