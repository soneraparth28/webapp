<?php


namespace App\Services\Brand;


use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\DeliverySettingService;

class BrandSettingService extends DeliverySettingService
{
    public function show($context = null)
    {
        return resolve(SettingRepository::class)
            ->getDeliverySettingLists(
                [$context ?? optional(brand()->defaultDeliverySettings)->value, 'brand_own_default_mail_email_name'],
                get_class(brand()),
                brand()->id
            );
    }
}
