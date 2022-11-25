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
        // first we will check if the exception is registered by developer
        // if yes trigger, then we check if exception is framework exception
        // finally we handle the exception in a default way.

        $e = $this->e;
        $appException = new AppException;

        if ($appException->hasRegistered($e)) {
            return $appException->triggerRegisterException($e);
        }

        if ($appException->isCoreException($e)) {
            return $appException->triggerCoreException($e);
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