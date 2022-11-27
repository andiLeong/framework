<?php

namespace Andileong\Framework\Core\Logs;

use Andileong\Framework\Core\Application;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BuildSingleLogDriver extends BuildLogDriver
{
    public function __construct(protected Application $application, protected array $config)
    {
        //
    }

    public function build()
    {
        $handler = new StreamHandler($this->config['path']);
        $handler->setFormatter($this->getLineFormatter());
        return new Logger($this->getChannelName(), [$handler]);
    }

}