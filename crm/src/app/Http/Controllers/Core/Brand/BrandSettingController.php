<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Brand\BrandSettingRequest as Request;
use App\Models\Core\App\Brand\Brand;
use App\Services\Core\Setting\DeliverySettingService;
use App\Services\Core\Setting\SettingService;

class BrandSettingController extends Controller
{
    protected $settingService;

    public function __construct(DeliverySettingService $service, SettingService $settingService, BaseFilter $filter)
    {
        $this->service = $service;
        $this->settingService = $settingService;
        $this->filter = $filter;
    }


    public function update(Request $request, Brand $brand)
    {
        if ($request->has('delivery'))
            collect($request->delivery)->all()->each(function ($value, $key) use($request, $brand) {
                $this->service->update(
                    $key,
                    $value,
                    $request->delivery['context'].'-'.$request->delivery['use_for'],
                    Brand::class,
                    $brand->id
                );
            });

        if ($request->has('privacy')) {
            $this->settingService->saveSettings(
                $request->privacy,
                'privacy',
                Brand::class,
                $brand->id
            );
        }

        return updated_responses('brand_settings');
    }
}
