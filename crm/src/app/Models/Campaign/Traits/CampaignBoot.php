<?php


namespace App\Models\Campaign\Traits;


use App\Models\Campaign\Campaign;
use App\Repositories\App\StatusRepository;

trait CampaignBoot
{
    public static function boot()
    {
        parent::boot();
        if (! app()->runningInConsole() ) {
            static::creating(function (Campaign $campaign) {
                $campaign->fill([
                    'created_by' => auth()->id(),
                    'status_id' => $campaign->status_id ?? resolve(StatusRepository::class)->campaignDraft()
                ]);
            });
        }
    }
}
