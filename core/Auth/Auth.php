<?php

namespace Andileong\Framework\Core\Auth;

interface Auth
{
    public function check() :bool;

    public function user();
}