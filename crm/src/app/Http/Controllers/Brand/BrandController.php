<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Services\Core\Brand\BrandService;

class BrandController extends Controller
{
    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
       return $this->service
            ->latest('id')
            ->get(['id', 'name']);
    }
}
