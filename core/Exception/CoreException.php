<?php

namespace Andileong\Framework\Core\Exception;

class CoreException
{
    protected $registeredException = [];

    public function __construct()
    {
        $this->setRegisteredExceptions();
    }

    /**
     * @return string[]
     */
    public function getRegisteredException(): array
    {
        return $this->registeredException;
    }
}