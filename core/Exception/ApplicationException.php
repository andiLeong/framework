<?php

namespace Andileong\Framework\Core\Exception;

use Exception;

class ApplicationException
{
    protected $registeredException = [];
    protected $coreException = [];

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

    public function hasRegistered(Exception $e)
    {
        return array_key_exists(get_class($e), $this->registeredException);
    }

    public function triggerRegisterException(Exception $e)
    {
        return $this->registeredException[get_class($e)]($e);
    }

    public function isCoreException(Exception $e)
    {
        return in_array(get_class($e), $this->coreException);
    }

    public function triggerCoreException(CoreExceptions $e)
    {
        return $e->render();
    }
}