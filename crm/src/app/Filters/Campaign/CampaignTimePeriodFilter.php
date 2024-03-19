<?php


namespace App\Filters\Campaign;


use App\Models\Campaign\Campaign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CampaignTimePeriodFilter
{

    public function filter(Campaign $campaign)
    {
        $method = 'whenItIs'.Str::studly($campaign->time_period);
        if (method_exists($this, $method)){
            return $this->$method($campaign);
        }
        return false;
    }

    public function whenItIsOnce(Campaign $campaign)
    {
        return false;
    }

    public function whenItIsImmediately(Campaign $campaign)
    {
        return false;
    }

    public function whenItIsHourly(Campaign $campaign)
    {
        logger($campaign->last_processed_at);
        logger(now());
        logger(Carbon::parse($campaign->last_processed_at)
            ->diffInHours(Carbon::now()));
        logger(Carbon::parse($campaign->last_processed_at)
                ->diffInHours(Carbon::now()) >= 1);

        if ($campaign->last_processed_at) {
            return Carbon::parse($campaign->last_processed_at)
                    ->diffInHours(Carbon::now()) >= 1;
        }
        return intval(Carbon::now()->format('i')) >= 0 && intval(Carbon::now()->format('i')) <= 15;
    }

    public function whenItIsDaily(Campaign $campaign)
    {
        return Carbon::parse($campaign->last_processed_at)
                    ->diffInDays(Carbon::now()) >= 1;
    }

    public function whenItIsWeekly(Campaign $campaign)
    {
        return Carbon::parse($campaign->last_processed_at)
                ->diffInWeeks(Carbon::now()) >= 1;
    }

    public function whenItIsMonthly(Campaign $campaign)
    {
        return Carbon::parse($campaign->last_processed_at)
                ->diffInMonths(Carbon::now()) >= 1;
    }

    public function whenItIsYearly(Campaign $campaign)
    {
        return Carbon::parse($campaign->last_processed_at)
                ->diffInYears(Carbon::now()) >= 1;
    }

}
