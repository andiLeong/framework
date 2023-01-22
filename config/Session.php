<?php


return [

    'default' => env('SESSION_DRIVER','file'),

    //session expire time default is 60 minute
    'expire' => env('SESSION_EXPIRE',60),

    'path' => storagePath() . '/framework/sessions',


    //the day and time that we perform clean up old expired session data
    //by default is every friday 08:00 to 11:00, you can pass multiple day eg, monday, thursday
    //if empty day array is provided we do clean up every request
    'flush_day' => [
        'day' => ['friday'],
        'time' => ['08:00', '11:00'],
    ],

    'name' => env('SESSION_NAME','awesome-session'),
];
