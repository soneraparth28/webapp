<?php

namespace App\Http\Controllers\Core\Brand;

use App\Helpers\Traits\UserBrand;
use App\Http\Controllers\Controller;

class UserBrandController extends Controller
{
    public function index()
    {
        return resolve(UserBrand::class)->brands();
    }
}
