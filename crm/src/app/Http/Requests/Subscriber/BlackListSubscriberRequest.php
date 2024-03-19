<?php

namespace App\Http\Requests\Subscriber;

use App\Helpers\Traits\BrandInactiveTrait;
use App\Http\Requests\AppRequest;
use App\Models\Subscriber\Subscriber;

class BlackListSubscriberRequest extends AppRequest
{
    use BrandInactiveTrait;
    public function authorize()
    {
        return $this->actionIfInactive();
    }

    public function rules()
    {
        return (new Subscriber())->blackListRules();
    }
}
