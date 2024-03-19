<?php

namespace App\Http\Requests\Core\Brand;

use App\Http\Requests\BaseRequest;
use App\Models\Core\App\Brand\BrandGroup;

class BrandGroupBrandRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return (new BrandGroup())
            ->brandAssignRules();
    }
}
