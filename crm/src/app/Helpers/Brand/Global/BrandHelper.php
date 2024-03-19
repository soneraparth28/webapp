<?php

use App\Models\Core\App\Brand\Brand;

if (!function_exists('brand')) {

    /**
     * @return Brand
     */
    function brand() {
        if (request()->has('brand_id') || optional(request()->route())->parameter('brand_id') || request()->get('brand_short_name')) {
            return Brand::finByShortNameOrIdCached(
                request()->get('brand_short_name') ?? request()->get('brand_id') ?? request()->route()->parameter('brand_id')
            );
        }
    }
}

