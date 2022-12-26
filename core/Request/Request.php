<?php

namespace Andileong\Framework\Core\Request;

class Request
{

    private array $headers;
    private array $query;
    private array $payload;
    private array $server;
    private array $all = [];
    private array $addOn = [];
    public static $test = false;

    public function __construct($query = [], $payload = [], $server = [], $headers = [])
    {
        if (!self::$test) {
            $headers = getallheaders();
            $query = $_GET;
            $payload = $this->parsePayload();
            $server = $this->removeEnv($_SERVER);
        }

        $this->query = $query;
        $this->payload = $payload;
        $this->server = $server;
        $this->headers = $headers;

        $this->all = array_merge($this->payload, $this->query);
    }

    /**
     * remove all env variable from server super variable
     * @param array $server
     * @return array
     */
    private function removeEnv(array $server)
    {
        foreach (array_keys($_ENV) as $value) {
            if (isset($server[$value])) {
                unset($server[$value]);
            }
        }
        return $server;
    }

    /**
     * @param array $query
     * @param array $payload
     * @param array $server
     */
    public static function setTest($query = [], $payload = [], $server = [], $headers = [])
    {
        self::$test = true;
        $instance = new static($query, $payload, $server, $headers);
        return $instance;
    }

    private function parsePayload()
    {
        if (!empty($_POST)) {
            return $_POST;
        }

//        $output = [];
        $json = json_decode(file_get_contents('php://input'), true);
//        parse_str($input, $output);

        if(is_null($json)){
            return [];
        }

        return $json;
    }

    /**
     * get a http method
     * @return mixed
     */
    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function query()
    {
        return $this->query;
    }

    /**
     * get a request query string
     * @return mixed|void
     */
    public function queryString()
    {
        if (isset($this->server['QUERY_STRING'])) {
            return $this->server['QUERY_STRING'];
        }
    }

    /**
     * determine if request has query string
     * @return bool
     */
    public function hasQueryString()
    {
        return !empty($this->queryString());
    }

    /**
     * get the request url without the query string
     * eg : http://example/com/test
     * @return string
     */
    public function url()
    {
        $url = $this->baseUrl() . $this->uriWithoutQuery();
        return rtrim($url, '/?');
    }


    /**
     * get the uri without the query string
     * @return mixed|string
     */
    private function uriWithoutQuery()
    {
        return $this->uri(true);
    }

    /**
     * get the uri with the query string
     * @return mixed|string
     */
    private function uriWithQuery()
    {
        return $this->uri();
    }

    /**
     * get the request uri
     * eg : example.com/test/1
     * return /test/1
     * @param bool $withoutQuery
     * @return mixed|string
     */
    protected function uri(bool $withoutQuery = false)
    {
        $uri = $this->server['REQUEST_URI'];
        if ($withoutQuery && $this->hasQueryString()) {
            return rtrim(preg_replace('/\?.+/', '', $uri), '/');
//            return rtrim(preg_replace('/\?[0-9a-zA-Z=\-_&\$\.\/+]+/', '', $uri), '/');
        }
        return $uri;
    }

    /**
     * get the full url include the query string
     * @return string
     */
    public function fullUrl()
    {
        $fullUrl = $this->baseUrl() . $this->uriWithQuery();
        return rtrim($fullUrl, '/?');
    }

    /**
     * get a url with added query String
     * @param array $query
     * @return string
     */
    public function urlWithQueryString(array $query)
    {
        $additional = http_build_query($query);
        $prefix = $this->hasQueryString() ? '&' : '?';
        return rtrim($this->fullUrl(), '/?') . $prefix . $additional;
    }

    /**
     * get the url without a certain query string
     * @param $keys
     * @return string
     */
    public function urlWithoutQueryString($keys)
    {
        if (!$this->hasQueryString()) {
            return $this->fullUrl();
        }

        $keys = is_array($keys) ? $keys : func_get_args();
        $query = $this->filter(fn($q, $key) => !in_array($key, $keys), $this->query);
        $additional = http_build_query($query);
        return $this->url() . '?' . $additional;
    }

    public function path()
    {
        $path = $this->uriWithoutQuery();
        return $path === '' ? '/' : $path;
    }

    /**
     * get the base url
     * eg : http://example.com
     * @return string
     */
    public function baseUrl()
    {
        $http = $this->isHttps() ? 'https' : 'http';
        return $http . "://" . $this->server['HTTP_HOST'];
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        $https = isset($this->server['HTTPS']) && !empty($this->server['HTTPS']);
        $on443 = isset($this->server['SERVER_PORT']) && $this->server['SERVER_PORT'] == 443;
        return $https || $on443;
    }

    /**
     * get the client ip
     * @return mixed
     */
    public function ip()
    {
        if (isset($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }

        if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        }

        return $this->server['REMOTE_ADDR'];
    }

    /**
     * rerun the payload of the request
     * @return array|mixed
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * get all the query and payload from the request
     * @param $key
     * @param $default
     * @return array|mixed|null
     */
    public function all($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->all;
        }

        if ($this->has($key)) {
            return $this->all[$key];
        }

        return $default;
    }

    /**
     * get the pure request data without addon
     * if you do not add data to payload you can safely use all()
     * @return array
     */
    public function original()
    {
        return array_diff_key($this->all, $this->addOn);
    }

    /**
     * merge additional item to request all data set
     * @param array $item
     * @return $this
     */
    public function merge(array $item)
    {
        $this->addOn = array_merge($this->addOn, $item);
        $this->all = array_merge($this->all, $item);
        return $this;
    }

    /**
     * merge to all data set only if the key not found
     * @param array $item
     * @return $this
     */
    public function mergeIfNotExist(array $item)
    {
        $item = array_diff_key($item, $this->all);
        return $this->merge($item);
    }

    /**
     * get the addon array
     * @return array
     */
    public function addOn()
    {
        return $this->addOn;
    }

    /**
     * determine if an array of keys are existed in the dataset
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        $shared = array_intersect(array_keys($this->all()), $keys);
        return count($shared) === count($keys);
    }

    /**
     * opposite the has
     * @param $key
     * @return bool
     */
    public function doesnotHas($key)
    {
        return !$this->has($key);
    }

    /**
     * check if the key really exist and value is not empty
     * @param $key
     * @return bool
     */
    public function exist($key)
    {
        $result = $this->get($key);
        return $result !== null && $result !== '';
    }

    /**
     * return subset of dataset according to the keys
     * @param $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->filter(
            fn($all, $key) => in_array($key, $keys),
        );
    }

    /**
     * return subset of data that exclude the keys
     * @param $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->filter(
            fn($all, $key) => !in_array($key, $keys),
        );
    }

    protected function filter($fn, $items = null)
    {
        $items ??= $this->all;
        return array_filter($items,
            fn($all, $key) => $fn($all, $key),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * trigger a callback if key is existed in the data set
     * @param $key
     * @param $fn
     * @param $default
     * @return mixed|null
     */
    public function whenHas($key, $fn, $default = null)
    {
        if ($this->has($key)) {
            return $fn($this->get($key));
        }

        return $default;
    }

    /**
     * get a single key from the all data set
     * @param $key
     * @param $default
     * @return array|mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->all($key);
        }
        return $default;
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function header($key, $default = null)
    {
        if ($this->hasHeader($key)) {
            return $this->headers[$key];
        }
        return $default;
    }

    public function hasHeader($key)
    {
        return isset($this->headers[$key]);
    }

    public function bearerToken($default = null)
    {
        $token = $this->header('Authorization');
        if (is_null($token)) {
            return $default;
        }

        if (str_starts_with($token, 'Bearer ')) {
            return substr($token, 7);
        }

        return $token;
    }

    /**
     * dynamic method to get attribute from the request payload
     * @param string $name
     * @return mixed|void
     */
    public function __get(string $name)
    {
        if ($this->has($name)) {
            return $this->all[$name];
        }
    }

    public function __set(string $name, $value)
    {
        return $this->mergeIfNotExist([$name => $value]);
    }

}
