<?php

namespace Andileong\Framework\Core\Auth\Contracts;

interface Guard
{
    /**
     * determine if the user is sign in
     * @return bool
     */
    public function check() :bool;

    /**
     * return the currently authenticated user instance
     * @return mixed
     */
    public function user();

    /**
     * attempt to log in
     * @param array $credential
     * @return boolean
     */
    public function attempt(array $credential);
}
