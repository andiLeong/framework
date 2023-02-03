<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:test', description: 'make a test')]
class MakeTest extends Command
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
        $this->addArgument('name', InputArgument::REQUIRED, 'name of the test');
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
        $fileName = $this->createFile($name, 'test', appPath() . '/core/tests/');
        $output->writeln("<info>$fileName Created Successfully</info>");

        return Command::SUCCESS;
    }

    private function getNameSpace(array $directories)
    {
        return rtrim('Andileong\\Framework\\Tests\\' . implode('\\', $directories), '\\');
    }

    private function getNewContent()
    {
        return function ($testName, $directoriesArray) {
            $this->replaceNameSpace($this->getNameSpace($directoriesArray))->replaceClassName('TestName', $testName);
            return $this->newContent;
        };
    }
}
