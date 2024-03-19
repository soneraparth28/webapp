<?php

namespace App\Http\Controllers\Core\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Brand\BrandGroupBrandRequest as Request;
use App\Models\Core\App\Brand\BrandGroup;
use App\Services\Core\Brand\BrandGroupService;

class BrandGroupBrandController extends Controller
{

    public function __construct(BrandGroupService $service)
    {
        $this->service = $service;
    }

    public function store(BrandGroup $brandGroup, Request $request)
    {
        $old = $brandGroup->brands;

        $this->service->attachBrand($brandGroup);

        attach_log_to_database('brand', 'brand_group', $old, $brandGroup->brands);

        return attached_response('brand');

    }

    public function show(BrandGroup $brandGroup)
    {
        return $brandGroup->brands;
    }

    public function destroy(Request $request)
    {
        $this->service->detachBrand();

        return detached_response('brand');
    }
}
