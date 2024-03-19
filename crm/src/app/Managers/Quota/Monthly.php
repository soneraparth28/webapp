<?php


namespace App\Managers\Quota;


class Monthly implements QuotaContract
{

    public function whenExists(int $count) : object
    {
        $probableDaily = (int)($count / 30);
        $probableHourly =  ($probableDaily / 24) >= 0 ? (int)($probableDaily / 24) : 0;
        return (object)[
            'hourly' => $probableHourly,
            'daily' => $probableDaily,
            'monthly' => $count,
        ];
    }

    public function whenNotExists(array $counts) : object
    {
        ['daily' => $daily] = $counts;
        
        $probableHourly = (int)( $daily / 24);

        return (object)[
            'hourly' => $probableHourly,
            'daily' => $daily,
            'monthly' => $daily * 30,
        ];
    }
}