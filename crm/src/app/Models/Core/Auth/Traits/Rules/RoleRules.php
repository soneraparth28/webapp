<?php


namespace App\Models\Core\Auth\Traits\Rules;


use Illuminate\Validation\Rule;

trait RoleRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:2|max:195',
            'type_id' => [
                'required'
            ],
            'brand_id' => [
                'nullable',
                Rule::exists('brands', 'id')
            ]
        ];
    }

    public function attachPermissionRules()
    {
        return [
            'permissions' => 'required'
        ];
    }
}
