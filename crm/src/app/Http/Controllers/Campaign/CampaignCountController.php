<?php


namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Services\Campaign\CampaignService;

class CampaignCountController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function index($last24Hours = 0)
    {
        return [
            'gross_count' => $this->service->count(),
            'status_wise' => $this->service->campaignsCount(
                $last24Hours
            )
        ];
    }
}
