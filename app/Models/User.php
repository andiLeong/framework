<?php

namespace App\Models;

use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\Support\Str;

class User extends Model
{
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * trigger before the create method
     */
    public function creating()
    {
        $attributes = [
            'remember_token' => Str::random(20),
            'avatar' => 'https://i.pravatar.cc/150?img=' . Arr::random(range(1, 70))
        ];

        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
