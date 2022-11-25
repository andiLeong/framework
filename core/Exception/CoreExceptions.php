<?php

namespace Andileong\Framework\Core\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class CoreExceptions extends \Exception
{
    protected $responseCode;

    public function render()
    {
        $this->responseCode = $this->getHttpStatusCode();
        return new JsonResponse(
            $this->getResponseMessage(),
            $this->responseCode
        );
    }

    public function getHttpStatusCode()
    {
        return array_key_exists($this->getCode(), Response::$statusTexts)
            ? $this->getCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getResponseMessage()
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->responseCode
        ];
    }
}