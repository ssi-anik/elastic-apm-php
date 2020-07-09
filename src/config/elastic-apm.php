<?php
return [
    'active'      => env('ELASTIC_APM_ACTIVE', true),
    'transaction' => [
        'type' => [
            'background' => 'queue',
            'foreground' => 'request',
        ],
    ],
];