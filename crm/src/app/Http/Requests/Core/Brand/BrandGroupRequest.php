<?php

namespace App\Http\Requests\Core\Brand;

use App\Http\Requests\BaseRequest;
use App\Models\Core\App\Brand\BrandGroup;

class BrandGroupRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new BrandGroup());
    }
}
