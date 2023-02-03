<?php

namespace Andileong\Framework\Core\Support;

class SubStrSeparator
{
    protected $count;
    protected $result = '';

    public function __construct(
        protected string $subject,
        protected string $separator,
        protected array  $portions = []
    ) {
        $this->count = strlen($this->subject);

        if (empty($this->portions)) {
            $this->portions = array_fill(0, $this->count, 1);
        }
    }

    /**
     * perform separation
     * @return string
     */
    public function separate()
    {
        foreach ($this->portions as $index => $length) {
            $value = substr($this->subject, $this->getStartPoint($index), $length);
            $this->append($value . $this->separator);
        }

        $this->appendRemaining();
        return rtrim($this->result, $this->separator);
    }

    /**
     * appends the value to the final result
     * @param $value
     * @return $this
     */
    protected function append($value)
    {
        $this->result .= $value;
        return $this;
    }

    /**
     * get the sub str starting point
     * @param $index
     * @return float|int
     */
    protected function getStartPoint($index)
    {
        return $index === 0
            ? 0
            : array_sum(array_slice($this->portions, 0, $index));
    }

    /**
     * appends value the final result
     * if we have the remaining letter
     * usually the sum portion array less than the actually subject count
     */
    private function appendRemaining()
    {
        $remaining = $this->count - (strlen($this->result) - count($this->portions));
        if ($remaining > 0) {
            $this->append(substr($this->subject, -$remaining));
        }
    }

    public function __toString(): string
    {
        return $this->separate();
    }
}
