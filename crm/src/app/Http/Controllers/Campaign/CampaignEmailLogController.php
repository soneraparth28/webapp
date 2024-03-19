<?php


namespace App\Http\Controllers\Campaign;


use App\Filters\Campaign\CampaignEmailLogFilter;
use App\Http\Controllers\Controller;
use App\Services\Campaign\CampaignService;

class CampaignEmailLogController extends Controller
{
    public function __construct(CampaignService $service, CampaignEmailLogFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function show($id)
    {
        $emailLogs = $this->service
            ->find($id)
            ->emailLogs
            ->load('subscriber:id,email', 'campaign:id,name', 'status:id,name,class');

        return $this->filter
            ->setCollection($emailLogs)
            ->init();
    }
}
