<?php

namespace App\Http\Controllers\Core\Brand;

use App\Http\Controllers\Controller;
use App\Services\Core\Brand\BrandService;

class BrandShortNameGeneratorController extends Controller
{
    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the incoming request.
     *
     * @return array
     */
    public function __invoke()
    {
        return $this->service
            ->generateShortNames();
    }
}
