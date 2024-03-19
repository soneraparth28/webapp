<?php


namespace App\Http\Controllers\Settings;


use App\Http\Controllers\Controller;
use App\Services\Settings\SettingsService;

class DeliveryAPIController extends Controller
{
    public function show()
    {
        if (brand() && $settings = brand()->mailSettings()) {
            return [
                'provider' => isset($settings['provider']) ? $settings['provider'] : ''
            ];
        }

        $settings = resolve(SettingsService::class)
            ->getMailSettings();

        return [
            'provider' => isset($settings['provider']) ? $settings['provider'] : ''
        ];
    }

    public function isExists()
    {
        $brandSettings = brand() ? brand()->mailSettings() : [];

        $settings = resolve(SettingsService::class)
            ->getMailSettings();

        return [
            'app' => count($settings),
            'brand' => count($brandSettings),
        ];
    }


}