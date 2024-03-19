<?php


namespace App\Http\Controllers\Campaign;


use App\Http\Controllers\Controller;
use App\Http\Requests\App\DateRangeRequest as Request;
use App\Models\Campaign\Campaign;
use App\Services\Email\EmailLogService;

class CampaignEmailStatisticsController extends Controller
{

    public function __construct(EmailLogService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request, $campaign_id, $range_type = 0)
    {
        if ($range_type === 'gross') {
            return $this->service->grossStats(
                $campaign_id
            );
        }
        return $this->service->stats( $range_type, $campaign_id );
    }
}
