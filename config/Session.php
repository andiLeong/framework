<?php


return [

    'default' => env('SESSION_DRIVER','file'),

    //session expire time default is 60 minute
    'expire' => env('SESSION_EXPIRE',120),

    'path' => storagePath() . '/framework/sessions',

    'name' => env('SESSION_NAME','awesome-session'),
];
