<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Brand\BrandUserRequest as Request;
use App\Models\Core\App\Brand\Brand;
use App\Services\Core\Brand\BrandService;

class BrandUserController extends Controller
{
    public function __construct(BrandService $service, BaseFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index(Brand $brand)
    {
        return $brand->users()
            ->filters($this->filter)
            ->paginate(
                \request('per_page', 25)
            );
    }

    public function store(Request $request, Brand $brand)
    {
        $old = $brand->users;

        $brand = $this->service->attachUser($brand);

        attach_log_to_database('user', 'brand', $old, $brand->users);

        return attached_response('user', [
            'brand' => $brand
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $old = $brand->users;

        $brand = $this->service->detachUser($brand);

        detach_log_to_database('user', 'brand', $old, $brand->users);

        return detached_response('user', [
            'brand' => $brand
        ]);
    }
}
