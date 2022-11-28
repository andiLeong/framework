<?php

namespace Andileong\Framework\Core\Stubs;

class CreateFileFromStubs
{
    private ?string $fileName;
    private array $newDirectoriesArray;

    public function __construct(
        protected $fileNameWithPath,
        protected $type = null,
        protected $location = null,
        protected $extension = null,
        protected $stubName = null
    )
    {
        $this->phraseNameAndDirectory();

        $this->setLocation($this->location);
        $this->setExtension($this->extension);
        $this->setStubName($this->stubName);
    }

    /**
     * handle the creation file from a stub
     * @param callable $fn
     * @return string
     */
    public function handle(callable $fn)
    {
//        $controllerPath = appPath() . '/app/Controller/';

        $possibleDirectories = implode('/', $this->newDirectoriesArray);
        ensureDirectoryExisted($this->location . $possibleDirectories);
//        return [$fileName, $possibleDirectoriesArray];
//        $content = str_replace([
//            '{Controller}',
//            '{NameSpace}'
//        ], [
//            $fileName,
//            $this->getNameSpace($directoriesArray)
//        ], $this->getStubContent());

        $content = $fn(
            $this->fileName,
            $this->newDirectoriesArray
        );

        $fullPathFileName = $this->location . $this->fileNameWithPath . '.' . $this->extension;
        file_put_contents($fullPathFileName, $content);
        return $fullPathFileName;
    }

    /**
     * phrase the name that mya contains the new directory
     * @return void
     */
    private function phraseNameAndDirectory(): void
    {
        $this->newDirectoriesArray = explode('/', $this->fileNameWithPath);
        $this->fileName = array_pop($this->newDirectoriesArray);
    }

    /**
     * get the stub content
     * @return false|string
     */
    public function getStubContent()
    {
        $stub = sprintf("%s/%s.stub", app('stubs_path'), $this->stubName);
        return file_get_contents($stub);
    }

    /**
     * set the path of the target file will locate
     * @param mixed|null $location
     */
    private function setLocation(mixed $location = null)
    {
        if (is_null($location)) {
            $this->location = sprintf("%s/app/%s/", appPath(), ucfirst($this->type));
        }
    }

    /**
     * set the file extension
     * @param $extension
     */
    private function setExtension($extension)
    {
        if (is_null($extension)) {
            $this->extension = 'php';
        }
    }

    /**
     * set stub name
     * @param $stubName
     */
    private function setStubName($stubName)
    {
        if (is_null($stubName)) {
            $this->stubName = ucfirst($this->type);
        }
    }
}