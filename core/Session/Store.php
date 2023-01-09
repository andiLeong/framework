<?php

namespace Andileong\Framework\Core\Session;

use Andileong\Framework\Core\Session\Contracts\Session;
use Andileong\Framework\Core\Support\Str;
use SessionHandlerInterface;

class Store implements Session
{

    protected $attributes = [];
    private mixed $rawSession = [];

    public function __construct(
        protected SessionHandlerInterface $handler,
        protected                         $id = null,
    )
    {
        $this->setId($id);
    }

    /**
     * session start and get session data from handler
     */
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

    /**
     * {@inheritdoc}
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritdoc}
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     * @param $key
     * @return false
     */
    public function delete($key)
    {
        if(!$this->exists($key)){
            return false;
        }

        unset($this->attributes[$key]);
        return true;
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    public function remove()
    {
        $this->attributes = [];
    }

    /**
     * {@inheritdoc}
     * @return array|mixed
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if ($this->exists($key)) {
            if ($this->get($key)) {
                return true;
            }
            return false;
        }

        return false;
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