<?php


namespace App\Services\TotalBrand;
use App\Models\Core\App\Brand\Brand;
use App\Services\AppService;

class TotalBrandService extends AppService
{
    public function __construct(Brand $brand)
    {
        $this->model = $brand;
    }
}
