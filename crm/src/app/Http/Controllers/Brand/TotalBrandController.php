<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Services\TotalBrand\TotalBrandService;

class TotalBrandController extends Controller
{
    public function __construct(TotalBrandService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
               ->count();
    }
}
