<?php

return [
    'paths' => [],
    'allowed_methods' => '*',
    'allowed_origins' => env('CORS_ALLOW_ORIGINS', '*'),
    'allowed_headers' => '*',
    'supports_credentials' => true,
];
