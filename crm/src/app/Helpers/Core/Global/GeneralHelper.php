<?php

use App\Helpers\Traits\HasAppRole;
use App\Helpers\Traits\UserBrand;

if (! function_exists('home_route')) {
    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return array
     */
    function home_route()
    {
        if (auth()->check()) {
            if (auth()->user()->isAppAdmin() || auth()->user()->can('manage_dashboard') || (new HasAppRole())->hasAppRole()) {
                return [
                    'route_name' => 'core.dashboard',
                    'route_params' => null
                ];
            }

            $brand = resolve(UserBrand::class)->brand();
            return [
                'route_name' => 'redirect.brand_dashboard',
                'route_params' => optional($brand)->short_name
            ];
        }
        return [
            'route_name' => 'users.login.index',
            'route_params' => null
        ];
    }
}
