<?php

namespace Andileong\Framework\Core\Session;

use Andileong\Framework\Core\Session\Contracts\Session;
use Andileong\Framework\Core\Support\Str;
use SessionHandlerInterface;

class Store implements Session
{

    protected $attributes = [];
    private mixed $rawSession;

    public function __construct(
        protected SessionHandlerInterface $handler,
        protected                         $id = null,
    )
    {
        $this->setId($id);
    }

    public function start()
    {
        $this->attributes = $this->rawSession = $this->getSessionFromHandler();
    }

    /**
     * get the session data from handler
     * @return array|mixed
     */
    protected function getSessionFromHandler()
    {
        $sessions = unserialize($this->handler->read($this->id));
        if ($sessions !== false) {
            return $sessions;
        }
        return [];
    }

    /**
     * set id
     * @param $id
     * @return $this
     */
    public function setId($id = null)
    {
        if (is_null($id)) {
            $this->id = $this->generateId();
        } else {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * generate a random string
     * @return string
     */
    public function generateId()
    {
        return Str::random(40);
    }

    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function get($key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    public function all()
    {
        return $this->attributes;
    }

    public function exists($key)
    {
        return isset($this->attributes[$key]);
    }

    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * persist all the attributes to session
     */
    public function save(): void
    {
        if (empty(array_diff_key($this->attributes, $this->rawSession))) {
            return;
        }
        $this->handler->write($this->id, serialize($this->attributes));
    }

    /**
     * clean up ald expired session data
     * @param $second
     */
    public function clean($second)
    {
        $this->handler->gc($second);
    }
}