<?php

namespace Andileong\Framework\Core\Exception;

use Andileong\Framework\Core\Application;
use App\Exception\AppException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler
{
    public function __construct(
        protected Application $app,
        protected Exception   $e,
    )
    {
        //
    }

    public function handle(): Response
    {
        $e = $this->e;
        $registeredExceptions = $this->app->get(AppException::class)->getRegisteredException();

        if (array_key_exists(get_class($e), $registeredExceptions)) {
            return $registeredExceptions[get_class($e)]($e);
        }

        return $this->handleDefaultException($e);

    }

    private function inProduction(): bool
    {
        return $this->app->isInProduction();
    }

    private function handleDefaultException($e)
    {
        if ($this->inProduction()) {
            $exception = [
                'message' => 'internal server error'
            ];
        } else {

            $exception = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'trace' => $e->getTrace(),
            ];
        }

        return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}