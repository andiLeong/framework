<?php

namespace App\Exception;

use Andileong\Framework\Core\Auth\Exception\JwtTokenExpiredException;
use Andileong\Framework\Core\Exception\Handler as CoreHandler;
use Andileong\Framework\Core\Jwt\Exception\JwtTokenValidationException;
use Andileong\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends CoreHandler
{
    /**
     * register developer needed exception handle
     */
    protected function register()
    {
        $this->customExceptions[ValidationException::class] = fn($e) => new JsonResponse($e->errors(), Response::HTTP_BAD_REQUEST);
        $this->customExceptions[JwtTokenValidationException::class] = fn($e) => new JsonResponse([
            'message' => $e->getMessage(),
            'code' => Response::HTTP_UNAUTHORIZED,
        ], Response::HTTP_UNAUTHORIZED);

        $this->customExceptions[JwtTokenExpiredException::class] = fn($e) => new JsonResponse([
            'message' => $e->getMessage(),
            'code' => Response::HTTP_UNAUTHORIZED,
        ], Response::HTTP_UNAUTHORIZED);
    }
}