<?php

namespace Andileong\Framework\Core\Pipeline;

use Andileong\Framework\Core\Request\Request;
use Closure;

abstract class Chainable
{
    protected $object;
    protected $message;
    protected $successor;

    abstract public function handle(?Request $object);

    /**
     * set up the next chain
     * @param Chainable $successor
     */
    public function setSuccessor(Chainable $successor)
    {
        $this->successor = $successor;
    }

    /**
     * remove the next chain on the line-up
     */
    public function removeNextChain()
    {
        $this->successor = null;
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
        $this->removeNextChain();
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
