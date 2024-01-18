<?php

return [
    'C_REST_CLIENT_ID' => env('C_REST_CLIENT_ID'),
    'C_REST_CLIENT_SECRET' => env('C_REST_CLIENT_SECRET'),
    'C_REST_WEB_HOOK_URL' => env('C_REST_WEB_HOOK_URL'),
    'C_REST_CURRENT_ENCODING' => env('C_REST_CURRENT_ENCODING'),
    'C_REST_IGNORE_SSL' => env('C_REST_IGNORE_SSL'),
    'C_REST_LOG_TYPE_DUMP' => env('C_REST_LOG_TYPE_DUMP'),
    'C_REST_BLOCK_LOG' => env('C_REST_BLOCK_LOG'),
    'C_REST_LOGS_DIR' => env('C_REST_LOGS_DIR'),
];

//define('C_REST_CLIENT_ID','local.5c8bb1b0891cf2.87252039');//Application ID
//define('C_REST_CLIENT_SECRET','SakeVG5mbRdcQet45UUrt6q72AMTo7fkwXSO7Y5LYFYNCRsA6f');//Application key
// or
//define('C_REST_WEB_HOOK_URL','https://rest-api.bitrix24.com/rest/1/doutwqkjxgc3mgc1/');//url on creat Webhook

//define('C_REST_CURRENT_ENCODING','windows-1251');
//define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
//define('C_REST_LOG_TYPE_DUMP',true); //logs save var_export for viewing convenience
//define('C_REST_BLOCK_LOG',true);//turn off default logs
//define('C_REST_LOGS_DIR', __DIR__ .'/logs/'); //directory path to save the log
