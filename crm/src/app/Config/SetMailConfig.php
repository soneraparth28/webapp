<?php


namespace App\Config;


use App\Config\Traits\UpdateMailSettings;
use App\Services\Settings\SettingsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SetMailConfig
{
    public $settings;

    use UpdateMailSettings;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function clear()
    {
        Artisan::call('config:clear');
        return $this;
    }

    public function set()
    {
        $settings = $this->getSettings();

        if (!empty($settings['provider'])) {
            $method = 'set'.Str::studly($settings['provider']);
            if (method_exists($this, $method)) {
                $this->{$method}($settings);
            }
        }

        return true;
    }


    /**
     * @return array
     */
    public function getSettings(): array
    {
        return count($this->settings) ? $this->settings : $this->getAppSettings();
    }

    public function getAppSettings()
    {
        return resolve(SettingsService::class)
            ->getMailSettings();
    }

}
