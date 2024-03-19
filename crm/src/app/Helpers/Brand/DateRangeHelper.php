<?php


namespace App\Helpers\Brand;


use Carbon\CarbonPeriod;

trait DateRangeHelper
{
    private function total()
    {
        return [
            'name' => 'total',
            'range' => [],
            'context' => 'Y'
        ];
    }
    private function last7Days()
    {
        return [
            'name' => 'last_7_days',
            'range' => [
                today()->subDays(7)->toDateTimeString(), now()->toDateTimeString()
            ],
            'context' => 'D'
        ];
    }
    private function thisWeek()
    {
        return [
            'name' => 'this_week',
            'range' => [
                now()->startOfWeek()->toDateTimeString(),
                now()->endOfWeek()->toDateTimeString(),
            ],
            'context' => 'D'
        ];
    }
    private function lastWeek()
    {
        return [
            'name' => 'last_week',
            'range' => [
                now()->subWeek()->startOfWeek()->toDateTimeString(),
                now()->subWeek()->endOfWeek()->toDateTimeString()
            ],
            'context' => 'D'
        ];
    }
    private function thisMonth()
    {
        return [
            'name' => 'this_month',
            'range' => [
                now()->startOfMonth()->toDateTimeString(),
                now()->endOfMonth()->toDateTimeString()
            ],
            'context' => 'Y-m-d'
        ];
    }
    private function lastMonth()
    {
        return [
            'name' => 'last_month',
            'range' => [
                now()->subMonth()->startOfMonth()->toDateTimeString(),
                now()->subMonth()->endOfMonth()->lastOfMonth()->toDateTimeString()
            ],
            'context' => 'Y-m-d'
        ];
    }

    private function thisYear()
    {
        return [
            'name' => 'this_year',
            'range' => [
                now()->startOfYear()->toDateTimeString(),
                now()->endOfYear()->toDateTimeString()
            ],
            'context' => 'M'
        ];
    }

    public function dateRange($type)
    {
        $context = [
            '0' => 'total',
            '1' => 'last7Days',
            '2' => 'thisWeek',
            '3' => 'lastWeek',
            '4' => 'thisMonth',
            '5' => 'lastMonth',
            '6' => 'thisYear'
        ][$type];
        
        return (object)$this->$context();
    }

    public function fillCounts($counts, object $dateRange, \Closure $callback)
    {
        $period = CarbonPeriod::create(...$dateRange->range);
        return collect($period->toArray())->map(function ($date) use ($counts, $dateRange, $callback)  {
            return $callback([
                'context' => $date->format($dateRange->context),
                'date' => $date->format('Y-m-d')
            ]);
        })->keyBy('context');
    }
}
