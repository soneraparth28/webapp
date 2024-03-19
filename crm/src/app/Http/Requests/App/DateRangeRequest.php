<?php


namespace App\Http\Requests\App;


use App\Http\Requests\AppRequest;

class DateRangeRequest extends AppRequest
{
    public function rules()
    {
        return [
            'range_type' => 'in:1,2,3,4,5,6,gross'
        ];
    }

    public function all($keys = null)
    {
        return array_replace_recursive(
            parent::all(),
            $this->route()->parameters()
        );
    }
}
