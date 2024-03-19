<?php


namespace App\Models\Core\App\Brand\Traits;


use App\Models\Core\Setting\Setting;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\DeliverySettingService;

trait BrandMethod
{
    public function similarShortNames($name)
    {
        return $this->query()
            ->whereRaw("short_name REGEXP '^{$name}(-[0-9]*)?$'")
            ->get(['short_name']);
    }

    public static function findByShortName($short_name)
    {
        return self::query()->where('short_name', $short_name)->first();
    }

    public static function finByShortNameOrIdCached($short_name)
    {
        return self::findByShortNameOrId($short_name);
    }

    public static function findByShortNameOrId($param)
    {
        return self::query()->where('short_name', $param)->orWhere('id', $param)->first();
    }

    public function mailSettings()
    {
        $defaultSettings = $this->defaultDeliverySettings;

        $settings = $this->deliverySettings->filter(function ($setting) use ($defaultSettings) {
            return $setting->context == optional($defaultSettings)->value;
        });

        if ($settings->count()){
            return resolve(SettingRepository::class)
                ->formatSettings(
                    $settings->merge($this->brandDeliveryMailSettingNameEmail),
                    true
                );
        }
        $service = resolve(DeliverySettingService::class);

        $default = $service
            ->getDefaultSettings('brand_default_mail');

        $provider = optional($default)->value;

        if (!$provider) {
            return [];
        }

        return $service
            ->getFormattedDeliverySettings([$provider, 'default_mail_email_name']);
    }

    public function isActive()
    {
        if ($this->status_id) {
            return $this->status->name != 'status_inactive';
        }
        return true;
    }

    public function isInactive()
    {
        return !$this->isActive();
    }
}
