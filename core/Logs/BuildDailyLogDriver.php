<?php

namespace Andileong\Framework\Core\Logs;

use Andileong\Framework\Core\Application;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class BuildDailyLogDriver extends BuildLogDriver
{
    public function __construct(protected Application $application, protected array $config)
    {
        //
    }

    public function build()
    {
        $handler = new RotatingFileHandler($this->config['path'], $this->config['days'] ?? 7);
        $handler->setFormatter($this->getLineFormatter());
        return new Logger($this->getChannelName(), [$handler]);
    }


}