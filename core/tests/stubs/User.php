<?php

namespace Andileong\Framework\Core\tests\stubs;

use Andileong\Framework\Core\Database\Model\Model;
use Andileong\Framework\Core\Support\Arr;
use Andileong\Framework\Core\Support\Str;

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
        $this->attributes['password'] = bcrypt($value);
    }

    public function getPasswordAttribute($value)
    {
        return 'access_' . $value;
    }

    public function scopeCountry($builder, $name)
    {
        $builder->where('location', $name);
    }

    public function creating()
    {
        $attributes = [
            'avatar' => 'https://i.pravatar.cc/150?img=' . Arr::random(range(1, 70))
        ];

       foreach ($attributes as $key => $value) {
          $this->{$key} = $value;
       }
    }
}