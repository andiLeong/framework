<?php

namespace Andileong\Framework\Core\Logs;

use Andileong\Framework\Core\Application;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerManager implements LoggerInterface
{
    protected $drivers = [];
    protected $supportedDrivers = ['daily','single'];
    protected $configs;

    public function __construct(protected Application $app)
    {
        $this->configs = $this->app->get('config')->get('log');
    }

    /**
     * try to get a driver instance
     * @param $driver
     * @return Logger
     * @throws Exception
     */
    public function getDriverInstance($driver = null) :logger
    {
        if(is_null($driver)){
            $driver = $this->configs['default'];
        }

        if(!in_array($driver,$this->supportedDrivers)){
            throw new Exception('log '. $driver .' driver not existed');
        }

        return $this->build($driver);
    }

    /**
     * build a monolog instance
     * @param $driver
     * @return Logger
     */
    public function build($driver) : logger
    {
        if(array_key_exists($driver,$this->drivers)){
           return $this->drivers[$driver];
        }

        $drivers = [
            'single' => BuildSingleLogDriver::class,
            'daily' => BuildDailyLogDriver::class
        ];

        return $this->drivers[$driver] = (new $drivers[$driver]($this->app,$this->configs['driver'][$driver]))->build();
    }

    /**
     * alias for getDriverInstance
     * @param $driver
     * @return Logger
     * @throws Exception
     */
    public function driver($driver)
    {
        return $this->getDriverInstance($driver);
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->emergency($message,$context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->alert($message,$context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->critical($message,$context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->error($message,$context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->warning($message,$context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->notice($message,$context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->info($message,$context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->debug($message,$context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->getDriverInstance()->log($level,$message,$context);
    }
}