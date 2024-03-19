<?php

namespace App\Http\Controllers\Campaign;

use App\Filters\Campaign\CampaignFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignRequest as Request;
use App\Models\Campaign\Campaign;
use App\Notifications\CampaignNotification;
use App\Services\Campaign\CampaignService;
use App\Services\Campaign\CampaignSubscribersService;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function __construct(CampaignService $service, CampaignFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->selectRaw(
                'id, name, subject, time_period, start_at, end_at, campaign_start_time, status_id, current_status'
            )
            ->with('status')
            ->latest('id')
            ->filters($this->filter)
            ->paginate(
                \request('per_page', 10)
            );
    }


    public function create()
    {
        return view('brands.campaign.create', ['tabInit' => \request('state', 0)]);
    }

    public function store(Request $request)
    {
        $campaign = DB::transaction(function () use ($request) {
            return $this->service
                ->setAttrs($request->except('attachments'))
                ->save();
        });

        notify()
            ->on('campaign_created')
            ->with($campaign)
            ->send(CampaignNotification::class);

        return created_responses('campaign', [
            'campaign' => $campaign->load('attachments')
        ]);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('attachments', 'audiences', 'status:id,name,class');

        if (\request('counts', false)) {
            $campaign->setAttribute(
                'counts',
                resolve(CampaignSubscribersService::class)
                    ->counts($campaign)
            );
        }

        if (\request()->get('load_audiences')) {
            $campaign->setAttribute(
                'list_subscriber_count',
                resolve(CampaignSubscribersService::class)
                    ->listSubscriberCount($campaign)
            );
        }

        return $campaign;
    }

    public function edit($id)
    {
        $message = null;
        if (!count(brand()->mailSettings()))
            $message = trans('default.no_mail_settings_response');

        return view('brands.campaign.create', [
            'id' => $id,
            'tabInit' => \request('state', 0),
            'message' => $message
        ]);
    }

    public function update(Request $request, $id)
    {
        $campaign = DB::transaction(fn() => $this->service
            ->processAttachment()
            ->update($request->all(), $id)
        );

        return updated_responses('campaign', [
            'campaign' => $campaign->load('attachments')
        ]);
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->update(['current_status' => 'archived'])) {
            notify()
                ->on('campaign_archived')
                ->with((object)$campaign->toArray())
                ->send(CampaignNotification::class);
            return response()->json(['message' => trans('default.the_campaign_has_done_successfully', ['action' => __t('archived')])]);
        }
        return failed_responses();
    }

    public function changeCurrentStatus(Campaign $campaign)
    {
        if (\request('status') && $campaign->current_status != 'archived') {
            $status = \request('status') == 'pause' ? 'paused'
                : (\request('status') == 'resume' ? 'active' : $campaign->current_status);
            $campaign->update(['current_status' => $status]);
            return response()->json(['message' => trans('default.the_campaign_has_done_successfully', ['action' => __t(\request('status') . 'd')])]);
        }
        return failed_responses();
    }
}
