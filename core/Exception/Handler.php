<?php

namespace Andileong\Framework\Core\Exception;

use Andileong\Framework\Core\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler
{
    protected $customExceptions = [];

    public function __construct(
        protected Application $app,
        protected Throwable   $e,
    )
    {
        $this->register();
    }

    public function register()
    {
        //
    }

    public function handle(): Response
    {
        // first we will check if the exception is registered by developer
        // if yes trigger, then we check if exception is framework exception
        // finally we handle the exception in a default way.

        $e = $this->e;

        if ($closure = $this->hasRegistered($e)) {
            return $this->triggerException($e, $closure);
        }

        if (method_exists($e, 'render')) {
            return $this->triggerException($e);
        }

        return $this->triggerDefaultException($e);

    }

    private function inProduction(): bool
    {
        return $this->app->isInProduction();
    }

    private function triggerDefaultException($e)
    {
        if ($this->inProduction()) {
            $exception = [
                'message' => 'internal server error',
                'code' => 500
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

    private function hasRegistered(Throwable $e)
    {
        $key = get_class($e);
        if (array_key_exists($key, $this->customExceptions)) {
            return $this->customExceptions[$key];
        }
    }

    private function triggerException(Throwable|CoreExceptions $e, $closure = null)
    {
        if ($closure) {
            return $closure($e);
        }

        return $e->render();
    }
}