<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Jobs\App\Template\ThumbnailGenerateJob;
use App\Models\Campaign\Campaign;
use App\Repositories\App\StatusRepository;

class CampaignDuplicateController extends Controller
{
    public function duplicate(Campaign $campaign)
    {
        $new = $campaign->replicate();
        $new->status_id = resolve(StatusRepository::class)->campaignDraft();
        $new->push();

        $campaign->relations = [];
        $campaign->load('audiences', 'attachments');

        foreach ($campaign->getRelations() as $name => $relation) {
            $newRelationShip = $relation->map(function ($relation) {
                $relation->toArray();
                unset($relation->id);
                unset($relation->fileable_type);
                unset($relation->fileable_id);
                unset($relation->campaign_id);
                return $relation;
            })->toArray();
            $new->{$name}()->createMany($newRelationShip);
        }

//        ThumbnailGenerateJob::dispatch($new, [
//            'contentKey' => 'template_content',
//            'fileableType' => 'campaign_template',
//            'customFolder' => 'thumbnails'
//        ])->onQueue('high')->onConnection('sync');

        return duplicated_response('campaign', ['campaign' => $new]);
    }
}
