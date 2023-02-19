<?php

namespace Andileong\Framework\Core\Support;

class Benchmark
{
    public static function start(array $functions)
    {
        $res = [];

        foreach ($functions as $key => $value) {
            gc_collect_cycles();
            $start = hrtime(true);
            $value();
            $time = (hrtime(true) - $start) / 1000000;
            $res[$key] = number_format($time, 3) . 'ms';
        }

        return $res;
    }
}
