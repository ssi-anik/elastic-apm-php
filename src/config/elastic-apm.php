<?php
return [
    'active'      => env('ELASTIC_APM_ACTIVE', true),
    // name for the transaction
    'transaction' => [
        'type' => [
            // A Job that will be processed by the Application
            'background' => 'queue',
            // Received from a user agent, and processed by the controllers & closures
            'foreground' => 'request',
        ],
    ],
    // When an error occurred
    'error'       => [
        // Number of files to be included in the backtrace.
        'trace_depth' => env('ELASTIC_APM_TRACE_DEPTH', 30),
    ],
    // types of Spans
    'types'       => [
        // When a request is served by the Laravel/Lumen application
        'request'    => 'request',
        // When a db query is ran
        'query'      => 'db',
        // When an error is captured the by the framework
        'error'      => 'error',
        // When an HTTP call is made to remote servers
        'http'       => 'external',
        // When a background job pulled from the queue.
        'background' => 'job',
    ],
];