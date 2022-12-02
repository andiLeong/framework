<?php

namespace Andileong\Framework\Core\Bootstrap;

use Andileong\Framework\Core\Application;
use ErrorException;

class HandleError
{
    public function bootstrap(Application $app)
    {
        error_reporting(-1);

        set_error_handler(function ($errno, $msg, $file, $line) {
            throw new ErrorException($msg, 0, $errno, $file, $line);
        });

        if ($app->isInProduction()) {
            ini_set('display_errors', 'Off');
        }
    }
}