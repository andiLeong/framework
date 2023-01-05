<?php

namespace Andileong\Framework\Core\tests\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandTest extends TestCase
{

    /** @test */
    public function it_can_make_controller()
    {
        $input = $this->getInput('make:controller', 'fooController');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Controller}'], [
            "App\\Controller",
            "FooController",
        ], $this->stubContent('Controller'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_resourceful_controller()
    {
        $input = $this->getInput('make:controller', 'ResourceController', [
            'resource' => 'resource'
        ]);
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Controller}'], [
            "App\\Controller",
            "ResourceController",
        ], $this->stubContent('ResourceController'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_controller_with_multiple_directory()
    {
        $input = $this->getInput('make:controller', 'Api/UserController');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Controller}'], [
            "App\\Controller\\Api",
            "UserController",
        ], $this->stubContent('Controller'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
        $this->assertTrue(rmdir(appPath() . '/app/Controller/Api'));
    }

    /** @test */
    public function it_can_make_model()
    {
        $input = $this->getInput('make:model', 'post');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Model}'], [
            "App\\Models",
            "Post",
        ], $this->stubContent('Model'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_command()
    {
        $input = $this->getInput('make:command', 'fooCommand');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Command}'], [
            "App\\Console\\Commands",
            "FooCommand",
        ], $this->stubContent('Command'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_test()
    {
        $input = $this->getInput('make:test', 'fooTest');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{TestName}'], [
            "Andileong\\Framework\\Tests",
            "FooTest",
        ], $this->stubContent('Test'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_middleware()
    {
        $input = $this->getInput('make:middleware', 'fooMiddleware');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{Middleware}'], [
            "App\\Middleware",
            "FooMiddleware",
        ], $this->stubContent('Middleware'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    /** @test */
    public function it_can_make_provider()
    {
        $input = $this->getInput('make:provider', 'fooServiceProvider');
        $output = $this->runCommand($input);

        [$fileContent, $filePath] = $this->parseFileFromResponse($output->fetch());
        $newContent = str_replace(['{NameSpace}', '{ProviderName}'], [
            "Andileong\\Framework\\Core\\Providers",
            "FooServiceProvider",
        ], $this->stubContent('Provider'));

        $this->assertEquals($fileContent, $newContent);
        $this->assertTrue(unlink($filePath));
    }

    public function parseFileFromResponse($response)
    {
        $path = str_replace([' Created Successfully', "\r", "\n"], '', $response);
        $content = file_get_contents($path);
        return [$content, $path];
    }

    public function stubContent($name)
    {
        return file_get_contents(app('stubs_path') . "/$name.stub");
    }

    public function getInput($command, $name, $option = [])
    {
        return new ArrayInput(array_merge([
            'command' => $command,
            'name' => $name,
        ], $option));
    }

    public function runCommand($input)
    {
//        dd(app());
        $console = app()->get('console');
        $output = new BufferedOutput();
        $console->runInTest($input, $output);
        return $output;
    }
}
