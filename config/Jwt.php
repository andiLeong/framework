<?php

return [
    //the hash secret key
    'secret' => env('JWT_SECRET', 'secret'),

    //set the default algo that available in the jwt website in lowercase
    'algorithm' => 'hs256',
];
