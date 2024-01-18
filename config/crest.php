<?php

return [
    'C_REST_CLIENT_ID' => env('C_REST_CLIENT_ID'),
    'C_REST_CLIENT_SECRET' => env('C_REST_CLIENT_SECRET'),
    'C_REST_WEB_HOOK_URL' => env('C_REST_WEB_HOOK_URL'),
    'C_REST_CURRENT_ENCODING' => env('C_REST_CURRENT_ENCODING'),
    'C_REST_IGNORE_SSL' => env('C_REST_IGNORE_SSL', true),
    'C_REST_LOG_TYPE_DUMP' => env('C_REST_LOG_TYPE_DUMP', true),
    'C_REST_BLOCK_LOG' => env('C_REST_BLOCK_LOG', true),
    'C_REST_LOGS_DIR' => env('C_REST_LOGS_DIR', './logs/'),
];
