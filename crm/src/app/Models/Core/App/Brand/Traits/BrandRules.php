<?php


namespace App\Models\Core\App\Brand\Traits;


trait BrandRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:2|max:195',
            'short_name' => 'required|min:3|unique:brands|alpha_num'
        ];
    }
    public function updatedRules()
    {
        return [
            'name' => 'required|min:2|max:195',
            'short_name' => 'required|min:3|unique:brands,short_name,' . request('brand')->id
        ];
    }

    public function associateUsersRules()
    {
        return [
            'users' => 'required|array|min:1',
            'roles' => 'required|array|min:1'
        ];
    }

}
