<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Core\Auth\Role\RoleController as BaseRoleController;
use Illuminate\Database\Eloquent\Builder;

class RoleController extends BaseRoleController
{
    public function index()
    {
        return $this->basic()
            ->with('users:id,first_name,last_name,email','users.profilePicture')
            ->paginate(request()->get('per_page', 10));
    }

    public function list()
    {
        return $this->basic()->get();
    }

    public function roles()
    {
        return $this->service
            ->select(['id', 'name', 'brand_id'])
            ->with('brand')
            ->orderBy('id')
            ->filters($this->filter)
            ->get();
    }

    public function brandRoles()
    {
        return $this->basic()
            ->select(['id', 'name'])
            ->get();
    }

    public function basic()
    {
        return $this->service
            ->orderBy('id')
            ->when(request()->brand_id, function (Builder $builder) {
                $builder->where('brand_id', request()->brand_id);
            }, function (Builder $builder) {
                $builder->whereNull('brand_id');
            })
            ->filters($this->filter);
    }
}
