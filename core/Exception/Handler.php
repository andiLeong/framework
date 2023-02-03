<?php

namespace Andileong\Framework\Core\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler
{
    protected $customExceptions = [];

    public function __construct(
        protected Throwable $e,
        protected Renderer  $renderer,
    ) {
        $this->register();
    }

    protected function register()
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
            return $this->renderer->renderClosure($closure);
        }

        if (method_exists($e, 'render')) {
            return $this->renderer->render('core');
        }

        return $this->renderer->render();
    }

    /**
     * check any exception is register
     * @param Throwable $e
     * @return mixed|void
     */
    private function hasRegistered(Throwable $e)
    {
        $key = get_class($e);
        if (array_key_exists($key, $this->customExceptions)) {
            return $this->customExceptions[$key];
        }
    }
}
