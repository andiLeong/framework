<?php

namespace Andileong\Framework\Core\Support;

abstract class Controller
{
    public function validate(array $rules)
    {
        return app('validator')->validate($rules);
    }
}
