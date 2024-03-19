<?php

namespace App\Http\Controllers\Core\Brand;

use App\Http\Controllers\Controller;
use App\Services\Core\Brand\BrandService;

class BrandDashboardRedirect extends Controller
{
    public function redirect($params = null)
    {
        $short_name = $params ?? request()->brand_dashboard;
        if (!$short_name){
            $authorized = get_authorized('manage_brand_dashboard');

            if (count($authorized) > 0)
                $brand = resolve(BrandService::class)->findOrFail($authorized[0]);
            else
                $brand = auth()->user()->brands->first();

            $short_name = $brand->short_name;
        }


        return redirect()->route('tenant.brand-dashboard', ['brand_dashboard' => $short_name]);

    }
}
