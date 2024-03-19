<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignDeliveryRequest as Request;
use App\Services\Campaign\CampaignService;

class CampaignDeliveryController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }
    public function store(Request $request, $id)
    {
        $this->service->update([
            'start_at' => null,
            'end_at' => null,
            'campaign_start_time' => null
        ], $id);

        $campaign = $this->service->update(
            $request->only('time_period', 'start_at', 'end_at', 'campaign_start_time'),
            $id
        );

        $audiences = $campaign->audiences->keyBy('audience_type');
        $audiences = [
            'subscribers' => $audiences->keys()->contains('subscriber')
                ? $audiences['subscriber']->audiences : [] ,
            'lists' => $audiences->keys()->contains('list')
                ? $audiences['list']->audiences : [],
        ];

        return updated_responses('campaign_delivery', [
            'campaign' => $campaign->setAttribute('audience', $audiences)
        ]);
    }


}
