<?php

namespace App\Http\Requests\Campaign;

use App\Http\Requests\AppRequest;
use App\Models\Campaign\Campaign;

class CampaignAudienceRequest extends AppRequest
{
    public function rules()
    {
        return (new Campaign())->audiencesRules();
    }
}
