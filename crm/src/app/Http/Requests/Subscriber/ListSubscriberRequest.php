<?php

namespace App\Http\Requests\Subscriber;

use App\Http\Requests\AppRequest;

class ListSubscriberRequest extends AppRequest
{
    public function rules()
    {
        return [
            'name' => 'min:2|required_without:lists',
            'lists' => 'array|required_without:name',
            'subscribers' => 'array|required',
            'subscribers.*' => 'exists:subscribers,id',
            'lists.*' => 'exists:lists,id',
            'isBulkAction' => 'boolean'
        ];
    }
}
