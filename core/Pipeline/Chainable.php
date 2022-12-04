<?php

namespace Andileong\Framework\Core\Pipeline;

use Closure;

abstract class Chainable
{
    protected $object;
    protected $message;
    protected $successor;

    abstract public function handle($object);

    /**
     * set up the next chain
     * @param Chainable $successor
     */
    public function setSuccessor(Chainable $successor)
    {
        $this->successor = $successor;
    }

    /**
     * trigger the next chain call
     * @param $object
     */
    public function next($object)
    {
        if ($this->successor) {
            $this->successor->handle($object);
        }

        $this->object = $object;
    }

    /**
     * break the chain
     * @param $message
     */
    public function break($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * create an anonymous chain object
     * @param Closure $closure
     * @return Chainable
     */
    public static function buildAnonymousChain(Closure $closure) : Chainable
    {
        $class = new class() extends Chainable {
            public $closure;

            public function handle($object)
            {
                return call_user_func($this->closure, $object, $this);
            }
        };
        $class->closure = $closure;
        return $class;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

}