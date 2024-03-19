<?php


namespace App\Models\Campaign\Traits;


use Illuminate\Validation\Rule;

trait CampaignRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:3',
            'subject' => 'required|min:3',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpeg,jpg,gif,png,pdf,zip',
            'status_id' => 'exists:statuses,id'
        ];
    }

    public function deliveryRules()
    {
        return [
            'time_period' => 'required|in:immediately,once,hourly,daily,weekly,monthly,yearly',
            'start_at' => 'required_if:time_period,hourly|nullable|date_format:Y-m-d',
            'end_at' => 'required_if:time_period,hourly|nullable|date_format:Y-m-d',
            'campaign_start_time' => [Rule::requiredIf(function () {
                return !in_array(request()->time_period, ['immediately', 'hourly']);
            }), 'nullable', 'date_format:H:i'],
        ];
    }

    public function audiencesRules()
    {
        return [
            'audiences.subscribers.*' => 'distinct|exists:subscribers,id|required_without:audiences.lists',
            'audiences.lists.*' => 'distinct|exists:lists,id|required_without:audiences.subscribers'
        ];
    }

    public function contentRules()
    {
        return [
            'template_content' => 'required',
        ];
    }

    public function confirmedRules()
    {
        $created_rules = $this->createdRules();
        $created_rules['attachments.*'] = '';
        return array_merge($created_rules, array_merge($this->deliveryRules(), $this->contentRules()));
    }
}
