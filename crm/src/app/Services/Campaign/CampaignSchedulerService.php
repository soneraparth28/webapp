<?php


namespace App\Services\Campaign;


use App\Filters\Campaign\CampaignTimePeriodFilter;
use App\Models\Campaign\Campaign;
use App\Models\Campaign\CampaignActivity;
use App\Models\Core\App\Brand\Brand;
use App\Repositories\App\StatusRepository;
use App\Services\AppService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignSchedulerService extends AppService
{
    public function campaigns()
    {
        $brands = Brand::query()
            ->whereHas('status', fn(Builder $builder) =>
                $builder->where('name', 'status_active')
            )->orDoesntHave('status')
            ->pluck('id');

        $statuses = resolve(StatusRepository::class)->campaignSentConfirmed();
        $campaigns = Campaign::with('attachments', 'brand:id,name')
            ->addSelect(['last_processed_at' => CampaignActivity::query()
                ->select('processed_at')
                ->whereColumn('campaign_id', 'campaigns.id')
                ->orderBy('id', 'DESC')
                ->limit(1)
            ])
            ->whereIn('status_id', $statuses)
            ->whereIn('brand_id', $brands)
            ->where('current_status', 'active')
            ->where(function (Builder $builder) {
                return $builder->where(function (Builder $builder) {
                    $builder->where(function (Builder $builder) {
                        $builder->whereRaw(
                            DB::raw("date(concat(`start_at`, ' ', `campaign_start_time`)) <= ?"),
                            [Carbon::now()->format('Y-m-d H:i:s')]
                        )->whereDate('end_at', '>=', Carbon::now()->format('Y-m-d'));
                    })->orWhereRaw(
                        DB::raw("IF(time_period != 'hourly' and time_period !='immediately' and time_period != 'once', IF(start_at is null and end_at is null and campaign_start_time <= ?, 1, 0), 0) = 1"), [Carbon::now()->format('H:i:s')]
                    )->orWhereRaw(
                        DB::raw(
                            "IF(time_period = 'hourly', IF(campaign_start_time is null and start_at is not null and end_at is not null and start_at <= ? and end_at >= ?, 1, 0), 0) = 1"
                        ),
                        [Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]
                    );
                })->orWhere('time_period', '=', 'immediately');
            })->get();

        return $campaigns->filter(function (Campaign $campaign) {
            if ($campaign->last_processed_at || $campaign->time_period == 'hourly'){
                return (new CampaignTimePeriodFilter())
                    ->filter($campaign);
            }
            return true;
        })->values();
    }
}
