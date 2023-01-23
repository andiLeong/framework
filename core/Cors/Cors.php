<?php

namespace Andileong\Framework\Core\Cors;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{

    public function __construct(protected Request $request, protected Config $config)
    {
        //
    }

    /**
     * add cors header to response if header Sec-Fetch-Mode contains cors
     * @param Response $response
     * @return Response
     */
    public function handleResponse(Response $response)
    {
        if($this->request->header('Sec-Fetch-Mode') !== 'cors'){
            return $response;
        }

        foreach ($this->getCorsOption() as $key => $value){
            $response->headers->set($key,$value);
        }

        return $response;
    }

    /**
     * handle the preflight option request middleware browser send to us
     * @param Chainable $middleware
     * @param Request $request
     * @return mixed
     */
    public function handleMiddlewareRequest(Chainable $middleware,Request $request)
    {
        if ($request->method() === 'OPTIONS'
            && $request->hasHeader('Access-Control-Request-Method')
        ) {

            $response = json('', 200, $this->getCorsOption());

            return $middleware->break($response);
        }

        return $middleware->next($request);
    }

    /**
     * get the cors options based on the config
     * @return array
     */
    protected function getCorsOption()
    {
        $config = $this->config;

        return [
            'Access-Control-Allow-Origin' => $this->getOrigin($this->request, $config->get('cors.allowed_origins')),
            'Access-Control-Allow-Credentials' => $config->get('cors.supports_credentials'),
            'Access-Control-Allow-Headers' => $config->get('cors.allowed_headers'),
            'Access-Control-Allow-Methods' => $config->get('cors.allowed_methods')
        ];
    }


    /**
     * parse the config allows origins
     * @param Request $request
     * @param $value
     * @return mixed|void|null
     */
    private function getOrigin(Request $request, $value)
    {
        if (str_contains('*', $value)) {
            return $value;
        }

        $allows = explode(',', $value);
        $host = $request->header('Origin');
        if (in_array($host, $allows)) {
            return $host;
        }
    }
}