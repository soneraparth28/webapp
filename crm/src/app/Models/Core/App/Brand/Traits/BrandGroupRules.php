<?php


namespace App\Models\Core\App\Brand\Traits;


use Illuminate\Validation\Rule;

trait BrandGroupRules
{
    public function createdRules()
    {
        return [
            'name' => 'required'
        ];
    }

    public function brandAssignRules()
    {
        return [
            'brand_id' => [
                'required',
                'integer',
                'not_in:0'
            ]
        ];
    }

}
