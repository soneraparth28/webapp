<?php


namespace App\Scopes;


use App\Exceptions\GeneralException;
use App\Models\Core\App\Brand\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BrandScope implements Scope
{

    /**
     * @param Builder $builder
     * @param Model $model
     * @throws GeneralException
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!app()->runningInConsole()){
            $brand_id = null;
            if ($brand_param = optional(request()->route())->parameter('brand_dashboard')) {
                $brand = Brand::finByShortNameOrIdCached($brand_param);

                if (!$brand) {
                    throw new GeneralException(trans('default.not_found', ['name' => trans('default.brand')]));
                }
                $brand_id = $brand->id;
            }

            $brand_id = $brand_id ?? request()->brand_id;

            $builder->when($brand_id, function (Builder $builder) use ($brand_id) {
                $builder->where('brand_id', '=', $brand_id);
            });
        }
    }
}
