<?php

namespace App\Http\Controllers\Core\Brand;

use App\Filters\Core\BaseFilter;
use App\Hooks\Settings\AfterDeliverySettingSaved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\DeliverySettingRequest as Request;
use App\Services\Core\Setting\DeliverySettingService;

class BrandDeliverySettingController extends Controller
{
    protected $service;

    public function __construct(DeliverySettingService $service, BaseFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }


    public function index($provider = null)
    {
        if (!$provider) {
            $default = $this->service->getDefaultSettings('brand_default_mail');
            $provider = optional($default)->value;
        }

        return $this->service
            ->getFormattedDeliverySettings([$provider, 'default_mail_email_name']);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     * @noinspection DuplicatedCode
     */
    public function update(Request $request)
    {
        $context = $request->get('provider');

        foreach ($request->only('from_name', 'from_email') as $key => $value) {
            $this->service
                ->update($key, $value, 'default_mail_email_name');
        }

        foreach ($request->except('allowed_resource', 'from_name', 'from_email') as $key => $value) {
            $this->service
                ->update($key, $value, $context);
        }

        $this->service->setDefaultSettings('brand_default_mail', $context);

        AfterDeliverySettingSaved::new()
            ->handle();

        return updated_responses('delivery_settings');
    }


}
