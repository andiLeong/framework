<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Andileong\Framework\Core\Stubs\CreateFileFromStubs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeModel extends Command
{
    use CreateFromStub;

    protected static $defaultName = 'make:model';

    /**
     * The command description
     *
     * @var string
     */
    protected static $defaultDescription = 'make a Model';

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
        (new SymfonyStyle($input, $output))->success($fileName . ' Created Successfully');

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