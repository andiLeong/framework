<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:model',description: 'make a model')]
class MakeModel extends Command
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

        $fileName = $this->createFile($name,'model',appPath() . '/app/Models/');
        $output->writeln("<info>$fileName Created Successfully</info>");

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Models\\' . implode('\\', $directories), '\\');
    }

    private function getNewContent()
    {
        return function ($modelName, $directoriesArray) {
            $this->replaceNameSpace($this->getNameSpace($directoriesArray))->replaceClassName('Model', $modelName);
            return $this->newContent;
        };
    }

}