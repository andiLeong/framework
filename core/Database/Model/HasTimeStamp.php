<?php

namespace Andileong\Framework\Core\Database\Model;

use Carbon\Carbon;

trait HasTimeStamp
{
    protected $timestamp = true;

    protected function setCreateTimestamp()
    {
        if ($this->timestamp) {
            $this->created_at = $this->updated_at = Carbon::now();
        }
    }

    protected function setUpdateTimestamp()
    {
        if ($this->timestamp) {
            $this->updated_at = Carbon::now();
        }
    }

    protected function getUpdateTimestampArray()
    {
        if ($this->timestamp) {
            $now = Carbon::now();
            $this->updated_at = $now;
            return ['updated_at' => $now];
        }

        return [];
    }
}
