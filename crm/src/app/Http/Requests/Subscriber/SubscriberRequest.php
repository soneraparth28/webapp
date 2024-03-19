<?php

namespace App\Http\Requests\Subscriber;

use App\Http\Requests\AppRequest;
use App\Models\Subscriber\Subscriber;

class SubscriberRequest extends AppRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new Subscriber());
    }
}
