<?php

namespace Andileong\Framework\Core\Support;

class ArrayStringSeparator
{
    protected $result = '';
    protected $array;

    public function __construct(
        protected string $subject,
        protected string $separator,
        protected array  $portions = []
    ) {
        $this->array = str_split($this->subject);
    }

    /**
     * perform separation
     * @return string
     */
    public function separate()
    {
        if (empty($this->portions)) {
            return $this->separateEveryValue();
        }

        foreach ($this->array as $index => $value) {
            $index = $index + 1;

            // here we check if the index equals to the potion fist value
            // if yes we append the separator, we also calculate the next 2 index and push the portion first value
            // so whenever $portions first value is always updated index in the array
            if ($this->needsToAppendSeparator($index)) {
                $value = $value . $this->separator;
                $this->calculatePortionIndex();
            }

            $this->append($value);
        }

        return rtrim($this->result, $this->separator);
    }

    /**
     * appends the value to the final result
     * @param $value
     */
    protected function append($value)
    {
        $this->result .= $value;
    }

    /**
     * check if we need to perform append the separator
     * @param $index
     * @return bool
     */
    protected function needsToAppendSeparator($index)
    {
        return $index === $this->portions[0];
    }

    /**
     * calculate the portion array first value
     */
    protected function calculatePortionIndex()
    {
        if (count($this->portions) >= 2) {
            $nextIndex = $this->portions[0] + $this->portions[1];
            unset($this->portions[0]);
            unset($this->portions[1]);
            array_unshift($this->portions, $nextIndex);
//                    dump($portions);
        }
    }

    /**
     * separate every value of the object
     * @return string
     */
    protected function separateEveryValue()
    {
        return implode($this->separator, $this->array);
    }

    public function __toString(): string
    {
        return $this->separate();
    }
}
