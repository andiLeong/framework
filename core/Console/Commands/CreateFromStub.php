<?php

namespace Andileong\Framework\Core\Console\Commands;

use Andileong\Framework\Core\Stubs\CreateFileFromStubs;

trait CreateFromStub
{
    public function createFile($nameWithLocation, $type = null, $location = null, $extension = 'php', $stubName = null)
    {
        $creator = new CreateFileFromStubs($nameWithLocation, $type, $location, $extension, $stubName);
        $this->oldContent = $creator->getStubContent();
        return $creator->handle(
            $this->getNewContent()
        );
    }

    public function replaceClassName($name, $replaceTo)
    {
        $this->newContent = str_replace('{' . $name . '}', $replaceTo, $this->newContent);
        return $this;
    }

    public function replaceNameSpace($replacedTo)
    {
        $this->newContent = str_replace('{NameSpace}', $replacedTo, $this->oldContent);
        return $this;
    }
}