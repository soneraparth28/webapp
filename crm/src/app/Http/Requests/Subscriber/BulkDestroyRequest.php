<?php


namespace App\Http\Requests\Subscriber;


use App\Helpers\Traits\BrandInactiveTrait;
use App\Http\Requests\AppRequest;

class BulkDestroyRequest extends AppRequest
{
    use BrandInactiveTrait;
    public function authorize()
    {
        return $this->actionIfInactive();
    }

    public function rules()
    {
        return [

        ];
    }
}