<?php

return [
    /**
     * Enable / Disable APM operations
     */
    'active'         => env('ELASTIC_APM_ACTIVE', true),
    /**
     * Enable Query logging to APM server
     */
    'send_queries'   => env('ELASTIC_APM_SEND_QUERIES', true),
    /**
     * Enable Redis Query logging to APM server
     */
    'send_redis'     => env('ELASTIC_APM_SEND_REDIS', false),
    /**
     * name for the transaction
     */
    'transaction'    => [
        'type' => [
            /**
             * A Job that will be processed by the Application
             */
            'background' => 'queue',
            /**
             * Received from a user agent, and processed by the controllers & closures
             */
            'foreground' => 'request',
        ],
    ],
    /**
     * 404 fallback, when no route match found.
     */
    'route_fallback' => 'index.php',
    /**
     * When an error occurred
     */
    'error'          => [
        /**
         * Number of files to be included in the backtrace.
         */
        'trace_depth' => env('ELASTIC_APM_TRACE_DEPTH', 30),
    ],
    /**
     * types of Spans
     */
    'types'          => [
        /**
         * When a request is served by the Laravel/Lumen application
         */
        'request'    => 'request',
        /**
         * When a db query is ran
         */
        'query'      => 'db',
        /**
         * When a redis query is ran
         */
        'redis'      => 'redis',
        /**
         * When an error is captured the by the framework
         */
        'error'      => 'error',
        /**
         * When an HTTP call is made to remote servers
         */
        'http'       => 'external',
        /**
         * When a background job pulled from the queue.
         */
        'background' => 'job',
    ],
];