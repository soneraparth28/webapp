<?php


namespace App\Http\Requests\Campaign;


use App\Http\Requests\AppRequest;
use App\Models\Campaign\Campaign;
use Illuminate\Validation\ValidationException;

class CampaignConfirmationRequest extends AppRequest
{
    protected function prepareForValidation()
    {
        $subscriber = !empty($this->counts['subscribers']) + !empty($this->counts['unsubscribed']);
        throw_if($subscriber < 1, ValidationException::withMessages([
            'audiences' => [trans('validation.gte.numeric', [
                'attribute' => strtolower(trans('default.audiences')),
                'value' => 1
            ])]
        ]));
    }

    public function rules()
    {
        return (new Campaign())->confirmedRules();
    }
}
