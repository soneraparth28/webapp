<?php


namespace App\Builder\Segment\Traits;


trait SegmentOperator
{
    public $operators = [
        'is' => '=',
        'is not' => '!=',
        'contains' => 'LIKE',
        'does not contain' => 'NOT LIKE',
        'starts with' => 'LIKE',
        'ends with' => 'LIKE',
        'does not start with' => 'NOT LIKE',
        'does not end with' => 'NOT LIKE',
        'on' => '=',
        'before' => '<',
        'after' => '>',
        'on or before' => '<=',
        'on or after' => '>=',
        'between' => 'between'
    ];


    public $operatorsType = [
        'is' => 'text',
        'is not' => 'text',
        'contains' => 'text',
        'does not contain' => 'text',
        'starts with' => 'text',
        'ends with' => 'text',
        'does not start with' => 'text',
        'does not end with' => 'text',
        'on' => 'date',
        'before' => 'date',
        'after' => 'date',
        'on or before' => 'date',
        'on or after' => 'date',
        'between' => 'date_range'
    ];
}
