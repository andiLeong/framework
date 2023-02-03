<?php

namespace Andileong\Framework\Core\Session;

use Andileong\Framework\Core\Session\Contracts\Session;
use Andileong\Framework\Core\Support\Str;
use Carbon\Carbon;
use SessionHandlerInterface;

class Store implements Session
{
    protected $attributes = [];

    public function __construct(
        protected SessionHandlerInterface $handler,
        protected array                   $config,
        protected                         $id = null,
    ) {
        $this->setId($id);
    }

    /**
     * session start and get session data from handler
     */
    public function start()
    {
        $this->attributes = $this->getSessionFromHandler();
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
        $id ??= $this->generateId();
        $this->id = $id;
        return $this;
    }

    /**
     * generate a random string
     * @return string
     */
    public function generateId(): string
    {
        return Str::random(32);
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
        return $this->exists($key)
            ? $this->attributes[$key]
            : $default;
    }

    /**
     * {@inheritdoc}
     * @param $key
     * @return false
     */
    public function delete($key)
    {
        if (!$this->exists($key)) {
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
        return $this->exists($key) && $this->get($key) !== null;
    }

    /**
     * persist all the attributes to session
     */
    public function save(): void
    {
        $this->handler->write($this->id, serialize($this->attributes));
    }

    /**
     * clean up ald expired session data
     * @param $second
     */
    public function clean($second)
    {
        if ($this->shouldPerformCleanUp()) {
            $this->handler->gc($second);
        }
    }

    /**
     * determine if we need to perform clean up old session data
     * @return bool
     */
    public function shouldPerformCleanUp()
    {
        $flushDay = $this->config['flush_day'];
        if (empty($flushDay['day'])) {
            return true;
        }

        $times = $flushDay['time'];
        foreach ($flushDay['day'] as $dayToFlush) {
            $now = Carbon::now();
            $startTime = Carbon::parse("$dayToFlush $times[0]");
            $endTime = Carbon::parse("$dayToFlush $times[1]");

            if ($now->dayName === ucfirst($dayToFlush) && $now->between($startTime, $endTime)) {
                return true;
            }
        }

        return false;
    }
}
