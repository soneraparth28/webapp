<?php


namespace App\Managers\Quota;


class Daily implements QuotaContract
{
    public function whenExists(int $count) : object
    {
        $hourly = $count <= 24 ? 1 : (int)($count / 24);
        return (object)[
            'hourly' => $hourly,
            'daily' => $count,
            'monthly' => $hourly * 24 * 30,
        ];
    }

    public function whenNotExists(array $counts) : object
    {
        ['hourly' => $hourly, 'monthly' => $monthly] = $counts;

        $daily = (int)($monthly / 30);

        return (object)[
            'hourly' => $daily / 24 <= $hourly ? 0 : (int)($daily / 24),
            'daily' => $daily < 1 ? 0 : $daily,
            'monthly' => $monthly,
        ];
    }
}