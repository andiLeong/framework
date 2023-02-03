<?php

namespace Andileong\Framework\Core\Logs;

use Monolog\Formatter\LineFormatter;

abstract class BuildLogDriver
{

    /**
     * @return mixed
     */
    protected function getChannelName(): mixed
    {
        if (isset($this->config['name'])) {
            return $this->config['name'];
        }

        return $this->isProduction ? 'production' : 'local';
    }

    protected function getDateFormat()
    {
        return "Y-n-j H:i:s";
    }

    protected function getLineFormatter()
    {
        return new LineFormatter(null, $this->getDateFormat(), true, true);
    }
}
