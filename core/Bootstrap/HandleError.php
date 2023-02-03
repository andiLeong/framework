<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Application;
use ErrorException;
use Throwable;

class HandleError
{
    /**
     * when app boots we do handling error
     * @param Application $app
     * @throws ErrorException
     */
    public function bootstrap(Application $app)
    {
        error_reporting(-1);

        set_error_handler(function ($errno, $msg, $file, $line) {
            throw new ErrorException($msg, 0, $errno, $file, $line);
        });

        set_exception_handler(
            fn (Throwable $e) => $this->handleException($e, $app)
        );

        register_shutdown_function(
            fn () => $this->handleShutDown($app)
        );

        ini_set('display_errors', 'Off');
    }

    /**
     * handle exception
     * @param Throwable $e
     * @param $app
     * @return mixed
     */
    public function handleException(Throwable $e, $app)
    {
        $handler = $app->get('exception.handler', [$e]);
        $app->get('logger')->error($e->getTraceAsString());
        return $handler->handle()->send();
    }

    /**
     * when app shutdown do error handling
     * @param $app
     * @return mixed|void
     */
    public function handleShutDown($app)
    {
        $error = error_get_last();
        if (is_null($error)) {
            return;
        }
        //dump($error['type']);

        if ($this->isDeprecation($error['type'])) {
            $app->get('logger')->error($error['message']);
            return;
        }

        if ($this->isFatal($error['type'])) {
            $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            return $this->handleException($e, $app);
        }
    }

    /**
     * check if error is fatal
     * @param $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

    /**
     * check if error is deprecation
     * @param $level
     * @return bool
     */
    protected function isDeprecation($level)
    {
        return in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]);
    }
}
