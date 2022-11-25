<?php

namespace App\Exception;

use Andileong\Framework\Core\Exception\CoreException;
use Andileong\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppException extends CoreException
{

    /**
     * register user need exception handle
     */
    public function setRegisteredExceptions()
    {
       $this->registeredException[ValidationException::class] = fn($e) => new JsonResponse($e->errors(),Response::HTTP_BAD_REQUEST);
    }
}