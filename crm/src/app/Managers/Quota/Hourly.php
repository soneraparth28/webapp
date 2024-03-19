<?php


namespace App\Managers\Quota;


class Hourly implements QuotaContract
{


    public function whenExists(int $count) : object
    {
        return (object)[
            'hourly' => $count,
            'daily' => $count * 24,
            'monthly' => $count * 24 * 30,
        ];
    }

    public function whenNotExists(array $counts) : object
    {

        ['daily' => $daily, 'monthly' => $monthly] = $counts;

        $hourly =  (int)($daily / 24);

        return (object)[
            'hourly' => $hourly,
            'daily' => $daily,
            'monthly' => $monthly,
        ];
    }
}
