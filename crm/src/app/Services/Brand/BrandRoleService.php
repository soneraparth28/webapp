<?php


namespace App\Services\Brand;


use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Role;
use App\Services\AppService;

class BrandRoleService extends  AppService
{
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function create(Brand $brand, $brandTypeId)
    {
        $this->model->fill([
            'name' => config('access.users.brand_admin_role'),
            'is_admin' => 1,
            'is_default' => 1,
            'type_id' => $brandTypeId,
            'brand_id' => $brand->id
        ]);
        if (app()->runningInConsole() || optional(request()->route())->getName() == 'install-demo-data') {
            $this->model->created_by = 1;
        }
        $this->model->save();

        return $this->model;
    }
}
