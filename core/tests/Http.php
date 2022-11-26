<?php

namespace Andileong\Framework\Core\tests;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\Routing\Router;

trait Http
{

    public function get($uri, $data = [])
    {
        return $this->call('get', $uri, $data);
    }

    public function call($method, $uri, $data = [])
    {
        [$method, $uri, $query, $payload] = $this->getRequestParams($method, $uri, $data);
        $request = Request::setTest($query, $payload, [
            'REQUEST_URI' => $uri, 'REQUEST_METHOD' => $method
        ]);

        $app = new Application(app('app_path'), $request);

        require_once './routes/routes.php';

        $response = $app['router']->run();
        return new Response($this, $response);
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