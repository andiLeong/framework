<?php

namespace Andileong\Framework\Core\Pipeline;

use Andileong\Framework\Core\Container\Container;

class Pipeline
{

    public array $pipes = [];
    public $object;

    public function __construct(protected ?Container $container = null)
    {
        //
    }

    /**
     * get the pipes
     * @param array $pipes
     * @return Pipeline
     * @throws \Exception
     */
    public function through(array $pipes)
    {
        $this->pipes = $this->normalizePipes($pipes);
        return $this;
    }

    /**
     * thing to send to the pipeline
     * @param $object
     * @return Pipeline
     */
    public function send($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * run the pipeline
     * @return $this
     */
    public function run()
    {
        return $this->connectPipe()->start();
    }

    /**
     * take the first pipe and start the chain call
     * @return $this
     */
    private function start()
    {
        $this->pipes[0]->handle($this->object);
        return $this;
    }

    /**
     * get the object back if no stopper
     * @return mixed
     */
    public function result()
    {
        if ($stopper = $this->getBrokenPipe()) {
            return $stopper->getMessage();
        }

        return $this->pipes[array_key_last($this->pipes)]->getObject();
    }

    /**
     * try to get a stopped pipe if any
     * @return mixed|void
     */
    protected function getBrokenPipe()
    {
        $broken = array_values(array_filter($this->pipes, fn(Chainable $pipe) => $pipe->getMessage() !== null));
        if (!empty($broken)) {
            return $broken[0];
        }
    }

    /**
     * execute the callback if the pipe is not broke
     * @param $fn
     * @return mixed
     */
    public function then($fn)
    {
        if ($stopper = $this->getBrokenPipe()) {
            return $stopper->getMessage();
        }

        $object = $this->pipes[array_key_last($this->pipes)]->getObject();
        return $fn($object);
    }

    /**
     * normalize each pipe and return the pipe array collection
     * @param array $pipes
     * @return array|null[]|object[]|string[]
     * @throws \Exception
     */
    private function normalizePipes(array $pipes)
    {
        return array_map(function ($pipe) {

            if ($pipe instanceof Chainable) {
                return $pipe;
            }

            if (is_callable($pipe)) {
                return Chainable::buildAnonymousChain($pipe);
            }

            if ($this->container) {
                return $this->container->get($pipe);
            }

            throw new \InvalidArgumentException('unrecognizable pipe ' . $pipe);
        }, $pipes);
    }

    /**
     * connect each pipe so that it can call the next pipe
     * @return $this
     */
    private function connectPipe()
    {
        $target = $this->pipes;
        foreach ($target as $key => $value) {
            $next = $key + 1;
            if ($next < count($this->pipes)) {
                $value->setSuccessor($this->pipes[$next]);
            }
        }
        return $this;
    }

}