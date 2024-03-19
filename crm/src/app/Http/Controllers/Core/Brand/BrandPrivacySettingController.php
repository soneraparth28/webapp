<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Core\BaseFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\BrandPrivacySettingRequest as Request;
use App\Services\Core\Setting\SettingService;

class BrandPrivacySettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service, BaseFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service->getFormattedSettings(
            config('settings.brand_default_prefix.privacy')
        );
    }

    public function update(Request $request)
    {
        $context = config('settings.brand_default_prefix')['privacy'];

        $this->service
            ->where('context', $context)
            ->delete();

        $privacy = [];
        foreach ($request->except('context', 'allowed_resource') as $key => $pr) {
            $privacy[$pr] = true;
        }

        $this->service->saveSettings(
            $privacy,
            $context
        );
        return updated_responses('privacy_settings');
    }


}
