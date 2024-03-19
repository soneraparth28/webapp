<?php


namespace App\Services\Core\Brand;


use App\Models\Core\App\Brand\Brand;
use App\Models\Core\App\Brand\BrandGroup;
use App\Services\Core\BaseService;

class BrandGroupService extends BaseService
{
    /**
     * @var Brand
     */
    protected $brand;

    public function __construct(BrandGroup $group, Brand $brand)
    {
        $this->model = $group;
        $this->brand = $brand;
    }

    public function attachBrand(BrandGroup $brandGroup)
    {
        $brand = $this->brand::query()
            ->findOrFail(request()->brand_id);
        if (!$brandGroup->brands->contains(request()->get('brand_id'))) {
            $brandGroup->brands()->save($brand);
        }
        return true;
    }

    public function detachBrand()
    {
        $brand = $this->brand::query()
            ->findOrFail(request()->brand_id);
        $brand->brand_group_id = null;
        $brand->save();

        return true;
    }

}
