<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeController extends Command
{
    protected static $defaultName = 'make:controller';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'make a controller';


    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'name of the controller');
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

        $stub = appPath() . DIRECTORY_SEPARATOR . 'core/Stubs/Controller.stub';
        $stubContent = file_get_contents($stub);
        $controllerPath = appPath() . DIRECTORY_SEPARATOR . 'app/Controller/';

        $possibleDirectoriesArray = explode('/', $name);
        $controllerName = array_pop($possibleDirectoriesArray);
        $possibleDirectories = implode('/', $possibleDirectoriesArray);
        if (!file_exists($controllerPath . '/' . $possibleDirectories)) {
            mkdir($controllerPath . '/' . $possibleDirectories, 0775, true);
        }

        $content = str_replace([
            '{Controller}',
            '{NameSpace}'
        ], [
            $controllerName,
            rtrim('App\\Controller\\' . implode('\\', $possibleDirectoriesArray), '\\')
        ], $stubContent);

        $fileName = $controllerPath . $name . '.php';

        file_put_contents($fileName, $content);

        $io = new SymfonyStyle($input, $output);
        $io->success($fileName . ' Created successfully');

        return Command::SUCCESS;
    }
}