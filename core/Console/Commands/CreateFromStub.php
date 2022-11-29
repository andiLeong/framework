<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Stubs\CreateFileFromStubs;

trait CreateFromStub
{
    /**
     * entry point to create file from the stub
     * @param $nameWithLocation
     * @param $type
     * @param $location
     * @param $extension
     * @param $stubName
     * @return string
     */
    public function createFile($nameWithLocation, $type = null, $location = null, $extension = 'php', $stubName = null)
    {
        $creator = new CreateFileFromStubs($nameWithLocation, $type, $location, $extension, $stubName);
        $this->oldContent = $creator->getStubContent();
        return $creator->handle(
            $this->getNewContent()
        );
    }

    /**
     * replace old content class name
     * @param $name
     * @param $replaceTo
     * @return $this
     */
    public function replaceClassName($name, $replaceTo)
    {
        $this->newContent = str_replace('{' . $name . '}', $replaceTo, $this->newContent);
        return $this;
    }

    /**
     * replace the old content name space
     * @param $replacedTo
     * @return $this
     */
    public function replaceNameSpace($replacedTo)
    {
        $this->newContent = str_replace('{NameSpace}', $replacedTo, $this->oldContent);
        return $this;
    }
}