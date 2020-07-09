<?php
return [
    'active'      => env('ELASTIC_APM_ACTIVE', true),
    'transaction' => [
        'type' => [
            'background' => 'queue',
            'foreground' => 'request',
        ],
    ],
    'error'       => [
        'trace_depth' => env('ELASTIC_APM_TRACE_DEPTH', 30),
    ],
    'types'       => [
        'query' => 'db',
        'error' => 'error',
        'http'  => 'external',
    ],
];