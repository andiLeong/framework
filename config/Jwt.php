<?php

return [
    'secret' => env('JWT_SECRET','secret'),
    'expire' => 3,
    'algorithm' => 'HS256',
];
