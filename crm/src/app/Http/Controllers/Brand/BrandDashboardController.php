<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandDashboardController extends Controller
{
    public function index()
    {
        return view('brands.index');
    }
}
