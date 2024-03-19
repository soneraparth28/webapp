<?php


namespace App\Models\Campaign\Traits;


use App\Models\Campaign\Campaign;

trait CampaignAudienceRelationship
{
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
