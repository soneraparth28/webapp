<?php


namespace App\Http\Requests\Campaign;


use App\Http\Requests\AppRequest;

class CampaignEmailLogRateRequest extends AppRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('type')) {
            $this->merge([
                'type' => $this->get('type')
            ]);
        }
    }
    public function rules()
    {
        return [
            'type' => 'in:clicked,opened'
        ];
    }
}
