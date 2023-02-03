<?php

namespace Andileong\Framework\Core\Exception;

use Andileong\Framework\Core\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Renderer
{
    public function __construct(
        protected Application              $app,
        protected Throwable|CoreExceptions $e,
    ) {
        //
    }

    /**
     * loop up table to where to render
     * @return \Closure[]
     */
    public function source()
    {
        return [
            'default' => fn () => $this->default(),
            'core' => fn () => $this->core(),
        ];
    }

    /**
     * render custom defined exception closure
     * @param $closure
     * @return mixed
     */
    public function renderClosure($closure)
    {
        return $closure($this->e);
    }

    /**
     * entry point to render
     * @param $type
     * @return mixed
     */
    public function render($type = 'default')
    {
        return $this->source()[$type]();
    }

    /**
     * render system core exceptions
     * @return JsonResponse
     */
    private function core()
    {
        return $this->e->render();
    }

    /**
     * render default exceptions
     * @return JsonResponse
     */
    private function default()
    {
        if ($this->inProduction()) {
            $exception = [
                'message' => 'internal server error',
                'code' => 500
            ];
        } else {
            $exception = [
                'message' => $this->e->getMessage(),
                'exception' => get_class($this->e),
                'file' => $this->e->getFile(),
                'line' => $this->e->getLine(),
                'trace' => $this->e->getTrace(),
            ];
        }

        return new JsonResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function inProduction(): bool
    {
        return $this->app->isInProduction();
    }
}
