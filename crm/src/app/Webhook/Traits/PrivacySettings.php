<?php


namespace App\Webhook\Traits;


use App\Helpers\Traits\HasMemoization;
use App\Services\Core\Setting\SettingService;

trait PrivacySettings
{
    use HasMemoization;

    public function settings()
    {
        return $this->memoize('privacy', function () {
            return (object)resolve(SettingService::class)
                ->getFormattedSettings(config('settings.brand_default_prefix')['privacy']);
        });
    }
}
