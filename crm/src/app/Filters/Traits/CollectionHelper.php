<?php


namespace App\Filters\Traits;


use Carbon\Carbon;

trait CollectionHelper
{

    public function inDateBetween($column, $range)
    {
        $range = is_array($range) ? $range : func_get_args();
        $start_at = $range[0] instanceof Carbon ? $range[0] : Carbon::parse($range[0]);
        $end_at = $range[1] instanceof Carbon ? $range[1] : Carbon::parse($range[1]);
        $date = $column instanceof Carbon ? $column : Carbon::parse($column);

        return $date->gte($start_at) && $date->lte($end_at);
    }
}
