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
        protected $extension = 'php',
        protected $stubName = null
    )
    {
        $this->phraseNameAndDirectory();

        $this->setLocation($this->location);
        $this->setStubName($this->stubName);
    }

    /**
     * handle the creation file from a stub
     * @param callable $fn
     * @return string
     */
    public function handle(callable $fn)
    {
        $possibleDirectories = implode('/', $this->newDirectoriesArray);
        ensureDirectoryExisted($this->location . $possibleDirectories, 0777);

        $content = $fn(
            $this->fileName,
            $this->newDirectoriesArray
        );

        file_put_contents($fullPathFileName = $this->getWriteableDestination(), $content);
        return $fullPathFileName;
    }

    /**
     * phrase the name that may contain the new directory
     * @return void
     */
    private function phraseNameAndDirectory(): void
    {
        $this->newDirectoriesArray = explode('/', $this->fileNameWithPath);
        $this->fileName = ucfirst(array_pop($this->newDirectoriesArray));
    }

    /**
     * get the file destination where will be written to disk
     * @return string
     */
    public function getWriteableDestination()
    {
        $newDirectory = implode('/',$this->newDirectoriesArray) . '/';
//        dump($newDirectory);
//        dd($this->fileName);
        return $this->location . $newDirectory . $this->fileName . '.' . $this->extension;
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