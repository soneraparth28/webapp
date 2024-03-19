<?php

namespace App\Http\Controllers\Role;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::query()
            ->where(function (Builder $builder) {
                $builder->whereNull('type_id')
                    ->orWhereHas('type', function (Builder $builder) {
                        $builder->where('alias', 'brand');
                    });
            })
            ->get()
            ->when(!request()->without_group, function (Collection $query) {
                return $query->groupBy(function ($permission) {
                    return $permission->group_name;
                });
            });
    }
}
