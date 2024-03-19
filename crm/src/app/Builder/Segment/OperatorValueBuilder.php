<?php


namespace App\Builder\Segment;


use App\Builder\Segment\Traits\SegmentOperator;
use App\Exceptions\GeneralException;
use Illuminate\Support\Str;

class OperatorValueBuilder
{
    use SegmentOperator;

    public function initialize($logic) : array
    {
        $method = Str::camel($logic[1]);
        if (method_exists($this, $method))
            return $this->{$method}($logic[2]);

        throw new GeneralException('Operator is not found '.$logic[1]);
    }

    public function on($value) : array
    {
        return [
            $this->operators['on'],
            $value
        ];
    }

    public function before($value) : array
    {
        return [
            $this->operators['before'],
            $value
        ];
    }

    public function after($value) : array
    {
        return [
            $this->operators['after'],
            $value
        ];
    }


    public function onOrBefore($value) : array
    {
        return [
            $this->operators['on or before'],
            $value
        ];

    }

    public function onOrAfter($value) : array
    {
        return [
            $this->operators['on or after'],
            $value
        ];
    }


    public function between($values) : array
    {
        return is_array($values) ? $values : explode(',', $values);

    }


    public function is($value) : array
    {
        return [
            $this->operators['is'],
            $value
        ];
    }

    public function isNot($value) : array
    {
        return [
            $this->operators['is not'],
            $value
        ];
    }

    public function contains($value) : array
    {
        return [
            $this->operators['contains'],
            "%$value%"
        ];
    }

    public function doesNotContain($value) : array
    {
        return [
            $this->operators['does not contain'],
            "%$value%"
        ];
    }

    public function startsWith($value) : array
    {
        return [
            $this->operators['starts with'],
            "$value%"
        ];
    }

    public function endsWith($value) : array
    {
        return [
            $this->operators['ends with'],
            "%$value"
        ];
    }

    public function doesNotStartWith($value) : array
    {
        return [
            $this->operators['does not start with'],
            "$value%"
        ];
    }

    public function doesNotEndWith($value) : array
    {
        return [
            $this->operators['does not end with'],
            "%$value"
        ];
    }




}
