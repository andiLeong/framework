<?php

namespace App\Console\Commands;

use Andileong\Framework\Core\Stubs\CreateFileFromStubs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeModel extends Command
{
    protected static $defaultName = 'make:model';

    /**
     * The command description
     *
     * @var string
     */
    protected static $defaultDescription = 'make a Model';


    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'name of the model');
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

        $creator = new CreateFileFromStubs($name, 'model', appPath() . '/app/Models/');
        $fileName = $creator->handle(fn($model, $directoriesArray) => str_replace([
            '{Model}',
            '{NameSpace}'
        ], [
            $model,
            $this->getNameSpace($directoriesArray)
        ], $creator->getStubContent())
        );

        $io = new SymfonyStyle($input, $output);
        $io->success($fileName . ' Created Successfully');

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Models\\' . implode('\\', $directories), '\\');
    }
}