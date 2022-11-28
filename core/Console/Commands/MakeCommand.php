<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Stubs\CreateFileFromStubs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeCommand extends Command
{

    protected static $defaultName = 'make:command';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'make a command';

    public function __construct(public Application $app)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'name of the command');
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

        $location = appPath() . '/app/Console/Commands/';
        $creator = new CreateFileFromStubs($name, 'command',$location);
        $fileName = $creator->handle(fn($commandName, $directoriesArray) => str_replace([
            '{Command}',
            '{NameSpace}'
        ], [
            $commandName,
            $this->getNameSpace($directoriesArray)
        ], $creator->getStubContent())
        );

        $io = new SymfonyStyle($input, $output);
        $io->success($fileName . ' Created successfully');

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Console\\Commands\\' . implode('\\', $directories), '\\');
    }
}