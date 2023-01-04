<?php

return [
    //the hash secret key
    'secret' => env('JWT_SECRET','secret'),

    //default token expire time in seconds , default is 3 hours
    'expire' => 60 * 60 * 3,

    //set the default algo that available in the jwt website in lowercase
    'algorithm' => 'hs256',
];
