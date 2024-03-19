<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CampaignTemplateRequest as Request;
use App\Jobs\App\Template\ThumbnailGenerateJob;
use App\Services\Campaign\CampaignService;

class CampaignTemplateController extends Controller
{
    public function __construct(CampaignService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request, $id)
    {
        $campaign = $this->service->update(
            ['template_content' => $request->get('template_content')],
            $id
        );
//        ThumbnailGenerateJob::dispatch($campaign, [
//            'contentKey' => 'template_content',
//            'fileableType' => 'campaign_template',
//            'customFolder' => 'thumbnails'
//        ])->onQueue('high')->onConnection('sync');

        return updated_responses('campaign_content', [
            'campaign' => $campaign->load('audiences', 'attachments')
        ]);
    }
}
