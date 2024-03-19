<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignAudienceRequest as Request;
use App\Services\Campaign\CampaignService;

class CampaignAudienceController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request, $id)
    {
        activity()->withoutLogs(function() use ($id){
            $this->service->audiences($id);
        });
        return updated_responses('campaign_audiences');
    }
}
