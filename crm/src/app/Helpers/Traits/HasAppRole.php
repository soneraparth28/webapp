<?php


namespace App\Helpers\Traits;


use App\Models\Core\Auth\Role;
use App\Repositories\Core\Auth\UserRepository;

class HasAppRole
{
    public function hasAppRole($roles = [])
    {
        $roles  = count($roles) ? $roles : resolve(UserRepository::class)->getCachedRolesUser();

        return $roles->filter(function (Role $role) {
            return $role->type->alias == 'app';
        })->count();
    }
}
