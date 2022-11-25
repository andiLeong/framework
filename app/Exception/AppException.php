<?php

namespace App\Exception;

use Andileong\Framework\Core\Exception\ApplicationException;
use Andileong\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppException extends ApplicationException
{
    // framework handled exception
    protected $coreException = [
        \Andileong\Framework\Core\Exception\MysqlConnectionException::class
    ];

    /**
     * register user need exception handle
     */
    public function setRegisteredExceptions()
    {
        $this->registeredException[ValidationException::class] = fn($e) => new JsonResponse($e->errors(), Response::HTTP_BAD_REQUEST);
    }
}