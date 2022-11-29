<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command
{
    use CreateFromStub;

    protected $oldContent;
    protected $newContent;

    protected static $defaultName = 'make:controller';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'make a controller';

    public function __construct(public Application $app)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'name of the controller')
            ->addArgument('resource', InputArgument::OPTIONAL, 'Weather its resourceful controller');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $resource = $input->getArgument('resource');

        if ($resource) {
            $stubName = 'ResourceController';
        }

        $fileName = $this->createFile($name, 'controller', null, 'php', $stubName ?? null);
        $output->writeln("<info>$fileName Created Successfully</info>");

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Controller\\' . implode('\\', $directories), '\\');
    }

    private function getNewContent()
    {
        return function ($controllerName, $directoriesArray) {
            $this->replaceNameSpace($this->getNameSpace($directoriesArray))->replaceClassName('Controller', $controllerName);
            return $this->newContent;
        };
    }
}