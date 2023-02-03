<?php

namespace Andileong\Framework\Core\Support;

class Ulid
{
    public $characters = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    public const ENCODING_LENGTH = 32;
    public const SUFFIX_LENGTH = 16;
    public $timeLength = 10;

    protected $time;
    protected $suffix;
    protected $ulid;

    /**
     * generate a ulid
     * @param $uppercase
     * @return Ulid
     * @throws \Exception
     */
    public static function generate($uppercase = true)
    {
        $instance = new static();
        $now = (int) (microtime(true) * 1000);
        $time = '';
        $suffix = '';

        foreach (range(1, $instance->timeLength) as $value) {
            $mod = $now % static::ENCODING_LENGTH;
            $time = $instance->characters[$mod] . $time;
            $now = ($now - $mod) / static::ENCODING_LENGTH;
        }

        foreach (range(1, static::SUFFIX_LENGTH) as $value) {
            $generatedIndex = random_int(0, 31);
            $suffix .= $instance->characters[$generatedIndex];
        }

        if (!$uppercase) {
            $time = strtolower($time);
            $suffix = strtolower($suffix);
        }

        return $instance->setTime($time)
            ->setSuffix($suffix)
            ->setUlid($time . $suffix);
    }

    /**
     * determine if the ulid is valid
     * @param string $ulid
     * @return bool
     */
    public static function isValid(string $ulid): bool
    {
        if (strlen($ulid) !== 26) {
            return false;
        }

        return !preg_match('/[^abcdefghjkmnpqrstvwxyz0-9]/i', $ulid);
    }

    /**
     * @return mixed
     */
    public function getUlid()
    {
        return $this->ulid;
    }

    /**
     * @param mixed $ulid
     */
    public function setUlid($ulid)
    {
        $this->ulid = $ulid;
        return $this;
    }

    /**
     * @param mixed $time
     * @return Ulid
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @param mixed $suffix
     * @return Ulid
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getUlid();
    }
}
