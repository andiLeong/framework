<?php


return [

    'default' => env('SESSION_DRIVER','file'),

    //session expire time default is 60 minute
    'expire' => env('SESSION_EXPIRE',60),

    'path' => storagePath() . '/framework/sessions',


    //the day that we perform clean up old expired session data
    //by default is every friday, you can pass multiple day eg, monday, thursday
    //if empty array is provided we do clean up every request
    'flush_day' => ['friday'],

    'name' => env('SESSION_NAME','awesome-session'),
];
