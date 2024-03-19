<?php

return [

    'segment_names' => [
        'date' => [
            'created_at',
            'last_updated'
        ],
        'text' => [
            'first_name',
            'last_name',
        ]
    ],

    'segment_logic_operators' => [
        'date' => [
            'on',
            'before',
            'after',
            'on or before',
            'on or after',
        ],
        'date_range' => [
            'between'
        ],
        'text' => [
            'is',
            'is not',
            'contains',
            'does not contain',
            'starts with',
            'ends with',
            'does not start with',
            'does not end with',
        ]
    ]
];
