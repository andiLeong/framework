<?php

namespace App\Models;

use Andileong\Framework\Core\Database\Model\Model;

class User extends Model
{
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }

    public function getPasswordAttribute($value)
    {
        return 'pre_' . $value;
    }
}