<?php

namespace Andileong\Framework\Core\Console;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Application as ConsoleApplication;


class Console
{
    protected $systemCommands = [
        Commands\MakeController::class,
        Commands\MakeCommand::class,
        Commands\MakeModel::class,
        Commands\MakeTest::class,
        Commands\Serve::class,
        Commands\MakeMiddleware::class,
    ];

    protected $appCommands = [];

    public function __construct(public Application $app)
    {
        //
    }

    public function getAllCommands()
    {
        return array_merge($this->systemCommands, $this->appCommands);
    }

    public function resolveCommands()
    {
        return array_map(fn($command) => new $command($this->app), $this->getAllCommands());
    }

    public function run()
    {
        $application = new ConsoleApplication();
        $application->addCommands($this->resolveCommands());
        $application->run();
    }

    public function runInTest($input,$output)
    {
        $application = new ConsoleApplication();
        $application->setAutoExit(false);
        $application->addCommands($this->resolveCommands());
        $application->run($input, $output);
    }
}