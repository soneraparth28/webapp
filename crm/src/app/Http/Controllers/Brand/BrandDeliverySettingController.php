<?php

namespace App\Http\Controllers\Brand;

use App\Exceptions\GeneralException;
use App\Hooks\Settings\AfterDeliverySettingSaved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DeliverySettingRequest;
use App\Services\Brand\BrandSettingService;
use Illuminate\Support\Facades\Artisan;

class BrandDeliverySettingController extends Controller
{
    public function __construct(BrandSettingService $service)
    {
        $this->service = $service;
    }

    public function index($context = null)
    {
        return $this->service->show($context);
    }

    public function delete()
    {
        if(!brand()){
            throw new GeneralException(trans('default.action_not_allowed'));
        }
        brand()->brandDeliveryMailSettingNameEmail()->delete();
        brand()->deliverySettings()->delete();
        brand()->defaultDeliverySettings()->delete();

        Artisan::call('queue:restart');

        return response()->json(['status' => true, 'message' => trans('default.brand_default_delivery_settings_removed')]);
    }

    public function update(DeliverySettingRequest $request)
    {
        $context = $request->get('provider');

        foreach ($request->only('from_name', 'from_email') as $key => $value) {
            $this->service
                ->update(
                    $key,
                    $value,
                    'brand_own_default_mail_email_name',
                    get_class(brand()),
                    brand()->id
                );
        }

        foreach ($request->except('allowed_resource', 'from_name', 'from_email', 'brand_id') as $key => $value) {
            $this->service
                ->update(
                    $key,
                    $value,
                    $context,
                    get_class(brand()),
                    brand()->id
                );
        }

        $this->service->setDefaultSettings(
            'brand_own_default_delivery_settings',
            $context,
            'brand_mail',
            get_class(brand()),
            brand()->id
        );
        AfterDeliverySettingSaved::new()
            ->handle();

        return updated_responses('delivery_settings');
    }
}
