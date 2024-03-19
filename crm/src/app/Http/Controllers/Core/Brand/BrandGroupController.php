<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Brand\BrandGroupFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\App\Brand\BrandGroup;
use App\Services\Core\Brand\BrandGroupService;
use App\Http\Requests\Core\Brand\BrandGroupRequest as Request;

class BrandGroupController extends Controller
{
    public function __construct(BrandGroupService $service, BrandGroupFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function list()
    {
        return view('application.views.brands.brand_group');
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest('id')
            ->withCount('brands')
            ->paginate(
                \request('per_page', 10)
            );
    }


    public function store(Request $request)
    {
        $brand_group = $this->service
            ->save();
        return created_responses('brand_group', [
            'brand_group' => $brand_group
        ]);
    }

    public function show(BrandGroup $brandGroup, Request $request)
    {
        return $brandGroup;
    }

    public function update(BrandGroup $brandGroup, Request $request)
    {
        $this->service->setModel($brandGroup);
        $brandGroup = $this->service->save();
        return updated_responses('brand_group', [
            'brand_group' => $brandGroup
        ]);
    }

    public function destroy(BrandGroup $brandGroup)
    {
        $brandGroup->delete();

        return deleted_responses('brand_group');
    }

}
