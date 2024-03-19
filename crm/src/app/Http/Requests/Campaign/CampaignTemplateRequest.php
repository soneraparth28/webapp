<?php

namespace App\Http\Requests\Campaign;

use App\Models\Campaign\Campaign;
use Illuminate\Foundation\Http\FormRequest;

class CampaignTemplateRequest extends FormRequest
{
    public function rules()
    {
        return (new Campaign())->contentRules();
    }
}
