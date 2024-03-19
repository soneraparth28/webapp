<?php


namespace App\Helpers\Traits;


use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Role;
use Illuminate\Database\Eloquent\Collection;

class UserBrand
{
    public function brands()
    {
        if ((new HasAppRole())->hasAppRole())
            return Brand::select(['id','name','short_name'])->get();

        return cache()->rememberForever('user-brands', function () {
            return auth()->user()->roles->load('brand:id,name,short_name')->map(function (Role $role) {
                return $role->brand;
            });
        });
    }

    public function brand($short_name = null)
    {
        return $this->brands()->when($short_name, function (Collection $collection) use ($short_name) {
            return $collection->where('short_name', $short_name);
        }, function (Collection $collection) {
            if (auth()->user()->brands->count()) {
                return $collection->where('id', auth()->user()->brands[0]->id);
            }
        })->first();
    }
}
