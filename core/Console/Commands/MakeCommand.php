<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:command',description: 'make a command')]
class MakeCommand extends Command
{
    use CreateFromStub;

    protected $oldContent;
    protected $newContent;

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
        $fileName = $this->createFile($name, 'command', $location);
        $output->writeln("<info>$fileName Created Successfully</info>");

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Console\\Commands\\' . implode('\\', $directories), '\\');
    }

//    private function getNewContent($oldContent)
//    {
//        return fn($name, $directoriesArray) => str_replace([
//            '{Command}',
//            '{NameSpace}'
//        ], [
//            $name,
//            $this->getNameSpace($directoriesArray)
//        ], $oldContent
//        );
//    }

    private function getNewContent()
    {
        return function ($controllerName, $directoriesArray) {
            $this->replaceNameSpace($this->getNameSpace($directoriesArray))->replaceClassName('Command', $controllerName);
            return $this->newContent;
        };
    }
}