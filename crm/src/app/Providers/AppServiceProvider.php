<?php

namespace App\Providers;

use App\Services\Settings\SettingsService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        if (!$this->app->environment('production') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        /*
         * Application locale defaults for various components
         *
         * These will be overridden by LocaleMiddleware if the session local is set
         */

        // setLocale for php. Enables ->formatLocalized() with localized values for dates
        setlocale(LC_TIME, config('app.locale_php'));

        // setLocale to use Carbon source locales. Enables diffForHumans() localized
        Carbon::setLocale(config('app.locale'));

        /*
         * Set the session variable for whether or not the app is using RTL support
         * For use in the blade directive in BladeServiceProvider
         */
        if (! app()->runningInConsole()) {
            if (config('locale.languages')[config('app.locale')][2]) {
                session(['lang-rtl' => true]);
            } else {
                session()->forget('lang-rtl');
            }
        }

        try {
            $settings = resolve(SettingsService::class)
                ->getCachedFormattedSettings();

            foreach ($settings as $key => $setting) {
                if ($key == 'company_name' && $setting) {
                    config()->set('app.name', $setting);
                }else{
                    if ($setting) {
                        config()->set('settings.application.'.$key, $setting);
                    }
                }
            }
        }catch (\Exception $exception) {}

    }
}
