<?php

namespace App\Exception;

use Andileong\Framework\Core\Exception\Handler as CoreHandler;
use Andileong\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends CoreHandler
{
    /**
     * register developer needed exception handle
     */
    public function register()
    {
        $this->customExceptions[ValidationException::class] = fn($e) => new JsonResponse($e->errors(), Response::HTTP_BAD_REQUEST);
    }
}