<?php


namespace App\Mail\Tag;


class CampaignTag extends Tag
{
    protected $campaign;

    public function __construct($campaign, $notifier, $receiver)
    {
        $this->campaign = $campaign;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = route('tenant.campaigns.view', [
            'campaign' => $this->campaign->id,
            'brand_dashboard' => $this->campaign->brand_id
        ]);
    }

    function notification()
    {
        return array_merge([
            '{name}' => $this->campaign->name,
        ], $this->common());
    }
}
