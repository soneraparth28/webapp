<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignConfirmationRequest as Request;
use App\Models\Campaign\Campaign;
use App\Notifications\CampaignNotification;
use App\Repositories\App\StatusRepository;
use App\Services\Campaign\CampaignService;

class CampaignConfirmationController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request, Campaign $campaign)
    {
        if ($campaign->status->name != 'status_draft') {
            return ['status' => false, 'message' => 'done'];
        }

        $attributes['status_id'] = resolve(StatusRepository::class)->campaignConfirmed();

        $campaign->update(
            $attributes
        );

        notify()
            ->on('campaign_confirmed')
            ->with($campaign)
            ->send(CampaignNotification::class);

        status_log_database('campaign', 'status_confirmed', $campaign);

        return [
            'status' => true,
            'message' => trans('default.confirmed', [
                'name' => trans("default.campaign")
            ]),
        ];
    }
}
