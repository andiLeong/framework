<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:middleware', description: 'make a middleware')]
class MakeMiddleware extends Command
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
        $this->addArgument('name', InputArgument::REQUIRED, 'name of the middleware');
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

        $fileName = $this->createFile($name, 'middleware');
        $output->writeln("<info>$fileName Created Successfully</info>");

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('App\\Middleware\\' . implode('\\', $directories), '\\');
    }

    private function getNewContent()
    {
        return function ($name, $directoriesArray) {
            $this->replaceNameSpace($this->getNameSpace($directoriesArray))->replaceClassName('Middleware', $name);
            return $this->newContent;
        };
    }
}
