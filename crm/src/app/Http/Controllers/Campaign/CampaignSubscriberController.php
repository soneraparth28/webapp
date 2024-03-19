<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign\Campaign;
use App\Services\Campaign\CampaignService;

class CampaignSubscriberController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function index(Campaign $campaign)
    {
        return $this->service
            ->subscribers($campaign);
    }
}
