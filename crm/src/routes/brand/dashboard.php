<?php

use App\Http\Controllers\Brand\BrandDashboardController;

Route::get('dashboard', [BrandDashboardController::class, 'index'])->name('brand-dashboard');
