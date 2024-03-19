<?php

namespace App\Console\Commands;

use App\Helpers\Traits\HasMemoization;
use App\Jobs\Mail\CampaignMailSender;
use App\Models\Campaign\Campaign;
use App\Models\Subscriber\Subscriber;
use App\Services\Campaign\CampaignActivityService;
use App\Services\Campaign\CampaignSchedulerService;
use App\Services\Campaign\CampaignSubscribersService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CampaignMailScheduler extends Command
{
    use HasMemoization;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will schedule all mail of campaign';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        resolve(CampaignSchedulerService::class)
            ->campaigns()
            ->each(function (Campaign $campaign) {
                if (count($campaign->brand->mailSettings())){
                    $subscribers = $this->campaignSubscribers($campaign);
                    $subscriber_count = $subscribers->count();
                    logger(Carbon::now()->format('Y-m-d h:i:s'));
                    logger($campaign->name);
                    if ($subscriber_count) {
                        $status_processing = resolve(\App\Repositories\App\StatusRepository::class)->getStatusId('campaign', 'status_processing');
                        $campaign->update(['status_id' => $status_processing]);
                    }
                    $subscribers->map(function (Subscriber $subscriber, $index) use ($campaign, $subscriber_count) {
                        $last = $subscriber_count == ($index + 1);
                        CampaignMailSender::dispatch(
                            $campaign,
                            $subscriber,
                            $last
                        )->onQueue('high');
                    });
                    resolve(CampaignActivityService::class)
                        ->updateActivity($campaign);
                }
            });
    }

    /**
     * @param Campaign $campaign
     * @return Collection
     */
    public function campaignSubscribers(Campaign $campaign)
    {
        return $this->memoize('campaign-'.rand(0, 999).$campaign->id, function () use ($campaign) {
            return resolve(CampaignSubscribersService::class)
                ->subscribers($campaign)
                ->get()
                ->unique('email')
                ->values();
        });
    }
}
