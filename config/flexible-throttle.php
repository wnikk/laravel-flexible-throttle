<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Attempts
    |--------------------------------------------------------------------------
    |
    | This value determines the maximum number of requests that can be made
    | within the specified decay time from ip address.
    | Once this limit is reached, the user will receive a 429 response.
    |
    */
    'max_attempts' => 10,

    /*
    |--------------------------------------------------------------------------
    | Decay Seconds
    |--------------------------------------------------------------------------
    |
    | This value determines the number of seconds until the rate limit resets.
    | For example, if set to 60, the user can make up to 'max_attempts' requests
    | per minute.
    |
    */
    'decay_seconds' => 30,

    /*
    |--------------------------------------------------------------------------
    | Block Duration
    |--------------------------------------------------------------------------
    |
    | This value determines the number of seconds a ip will be blocked if they
    | exceed the maximum number of allowed requests. During this time, any
    | request from the user will receive a 429 Too Many Requests response.
    |
    */
    'block_duration' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | This section allows you to define different rate limiting rules for
    | specific HTTP status codes. For example, you can set stricter limits
    | for 403 Forbidden responses to prevent abuse.
    |
    | Each exception should have its own 'max_attempts', 'decay_seconds', and
    | 'block_duration' values.
    |
    */
    'rules' => [

        /*
        |--------------------------------------------------------------------------
        | HTTP Status Codes
        |--------------------------------------------------------------------------
        |
        | These settings apply to requests that result in specific HTTP status
        | codes. You can set different rate limiting rules for each status code.
        |
        */
        'status_codes' => [
            '403',
            '404' => [
                'max_attempts' => 20,
                'decay_seconds' => 30,
                'block_duration' => 1800,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Exceptions
        |--------------------------------------------------------------------------
        |
        | These settings apply to specific exceptions. You can set different
        | rate limiting rules for each exception class.
        |
        */
        'exceptions' => [
            \RuntimeException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => [
                'max_attempts' => 20,
                'decay_seconds' => 30,
                'block_duration' => 1800,
            ],
        ],
    ],
];