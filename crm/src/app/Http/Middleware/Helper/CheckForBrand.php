<?php


namespace App\Http\Middleware\Helper;


use App\Exceptions\GeneralException;
use App\Helpers\Traits\HasAppRole;
use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Role;
use App\Repositories\Core\Auth\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait CheckForBrand
{
    /**
     * @param Request $request
     * @return bool
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function checkForAllowedBrand(Request $request)
    {
        $brand_param = $request->route()->parameter('brand_dashboard');
        $route_name = $request->route()->getName();

        /**@var $roles Collection*/
        $roles = resolve(UserRepository::class)->getCachedRolesUser();

        if ($this->checkAppType($roles))
            return true;

        $brandRoles = $roles->filter(function (Role $role) {
            return $role->type->alias == 'brand';
        });

        if (!$brandRoles->count()){
            throw new GeneralException(trans('default.action_not_allowed'));
        }

        if ($brand_param && strpos($route_name, 'tenant.') == 0) {
            $brand = Brand::finByShortNameOrIdCached($brand_param);

            if (!$brand) {
                throw new GeneralException(trans('default.not_found', ['name' => trans('default.brand')]));
            }

            $brandRoles = $roles->filter(function (Role $role) use ($brand) {
                return $role->brand_id == $brand->id;
            });

            if (!$brandRoles->count()){
                throw new GeneralException(trans('default.action_not_allowed'));
            }
            return true;
        }


        if (!$this->checkAppType($roles))
            throw new GeneralException(trans('default.action_not_allowed'));
    }

    /*
    *brand_dashboard route parameter either brand id or brand short_name
    *It set to null cause we dont't need to pass it in controller
    */
    public function byPassBrandDashboard(Request $request)
    {
        $brand_param = $request->route()->parameter('brand_dashboard');
        if ($brand_param) {
            $brand = Brand::finByShortNameOrIdCached($brand_param);
            $request->merge([
                'brand_id' => $brand->id,
                'brand_short_name' => $brand->short_name
            ]);
        }
        $request->route()->setParameter('brand_dashboard', null);
    }

    public function checkAppType($roles = [])
    {
        return (new HasAppRole())->hasAppRole($roles);
    }

}
