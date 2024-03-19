<?php


namespace App\Services\Settings;


use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\DeliverySettingService;
use App\Services\Core\Setting\SettingService;

class SettingsService extends SettingService
{
    public function getCachedFormattedSettings()
    {
        return cache()->remember('app-settings-global', 84000, function () {
            return resolve(SettingRepository::class)
                ->getFormattedSettings('app');
        });
    }

    public function getMailSettings()
    {
        $service = resolve(DeliverySettingService::class);

        $default = $service
            ->getDefaultSettings();

        return $service
            ->getFormattedDeliverySettings([
                optional($default)->value,
                'default_mail_email_name'
            ]);
    }
}
