<?php


namespace App\Http\Controllers\Campaign;


use App\Helpers\Traits\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignEmailLogRateRequest;
use App\Models\Campaign\Campaign;
use App\Services\Campaign\CampaignEmailLogService;
use App\Services\Campaign\CampaignService;
use App\Services\Campaign\CampaignSubscribersService;

class CampaignAPIController extends Controller
{
    use NumberHelper;

    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->latest('id')
            ->get(['id', 'name']);
    }

    public function view($id)
    {
        return view('brands.campaign.show', ['id' => $id]);
    }

    public function count(Campaign $campaign)
    {
        $count = resolve(CampaignSubscribersService::class)
            ->subscribers($campaign)
            ->count();

        return $count ? $this->numberFormatter($count) : 'N/A';
    }

    public function rates(Campaign $campaign, CampaignEmailLogRateRequest $request)
    {
        return resolve(CampaignEmailLogService::class)
            ->setModel($campaign)
            ->rates($request->type);
    }
}
