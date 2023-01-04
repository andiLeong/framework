<?php

namespace Andileong\Framework\Core\Auth;

interface Guard
{
    public function check() :bool;

    public function user();

    /**
     * attempt to log in
     * @param array $credential
     * @return boolean
     */
    public function attempt(array $credential);
}