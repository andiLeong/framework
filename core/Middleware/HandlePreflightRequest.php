<?php

namespace Andileong\Framework\Core\Middleware;

use Andileong\Framework\Core\Config\Config;
use Andileong\Framework\Core\Pipeline\Chainable;
use Andileong\Framework\Core\Request\Request;

class HandlePreflightRequest extends Chainable
{
    public function __construct(protected Config $config)
    {
        //
    }

    public function handle(Request|null $request)
    {
        if ($request->method() === 'OPTIONS'
            && $request->hasHeader('Access-Control-Request-Method')
        ) {

            $config = $this->config;

//            dd($config->get('cors.allowed_origins'));
            $response = json('', 200, [
                'Access-Control-Allow-Origin' => $this->parse($request, $config->get('cors.allowed_origins')),
                'Access-Control-Allow-Credentials' => $config->get('cors.supports_credentials'),
                'Access-Control-Allow-Headers' => $config->get('cors.allowed_headers'),
                'Access-Control-Allow-Methods' => $config->get('cors.allowed_methods')
            ]);

            return $this->break($response);
        }

        return $this->next($request);
    }

    /**
     * parse the config allows origins
     * @param Request $request
     * @param $value
     * @return mixed|void|null
     */
    private function parse(Request $request, $value)
    {
        if (str_contains('*', $value)) {
            return $value;
        }

        $allows = explode(',', $value);
        $host = $request->header('Origin');
        if(in_array($host,$allows)){
            return $host;
        }
    }
}