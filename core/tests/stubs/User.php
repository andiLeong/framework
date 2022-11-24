<?php

namespace Andileong\Framework\Core\tests\stubs;

use Andileong\Framework\Core\Database\Model\Model;

class User extends Model
{
    protected $appends = [
        'foo_bar',
        'no_appends',
    ];

    public function getFooBarAttribute($value)
    {
        return 'bar';
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }

    public function getPasswordAttribute($value)
    {
        return 'access_' . $value;
    }
}