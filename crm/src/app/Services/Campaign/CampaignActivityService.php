<?php


namespace App\Services\Campaign;


use App\Models\Campaign\Campaign;
use App\Models\Campaign\CampaignActivity;
use App\Services\AppService;
use Carbon\Carbon;

class CampaignActivityService extends AppService
{
    public function updateActivity(Campaign $campaign)
    {
        $campaign->campaignActivities()->save(
            new CampaignActivity([
                'processed_at' => Carbon::now(),
                'status_id' => $campaign->status_id
            ])
        );
    }
}
