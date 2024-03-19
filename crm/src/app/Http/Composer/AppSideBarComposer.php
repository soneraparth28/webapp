<?php


namespace App\Http\Composer;


use App\Http\Middleware\Helper\CheckForBrand;
use App\Services\Settings\SettingsService;
use Illuminate\View\View;

class AppSideBarComposer
{
    use CheckForBrand;

    public function compose(View $view)
    {
        return $view->with([
            'permissions' => [
                [
                    'name' => __t('dashboard'),
                    'url' => route('core.dashboard'),
                    'icon' => 'pie-chart',
                    'permission' => auth()->user()->can('manage_dashboard') && $this->checkAppType()
                ],
                [
                    'name' => __t('brands'),
                    'url' => route('brands.lists'),
                    'icon' => 'server',
                    'permission' => authorize_any(['view_brands', 'create_brands']) && $this->checkAppType()
                ],
                [
                    'name' => __t('brand_group'),
                    'url' => route('brand-group.lists'),
                    'icon' => 'aperture',
                    'permission' => authorize_any(['view_brand_groups', 'create_brand_groups']) && $this->checkAppType()
                ],
                [
                    'name' => __t('users_roles'),
                    'url' => route('users.lists'),
                    'icon' => 'user-check',
                    'permission' => authorize_any(['view_users', 'view_roles']) && $this->checkAppType()
                ],
                [
                    'name' => __t('settings'),
                    'id' => 'settings',
                    'permission' => authorize_any([
                        'view_settings',
                        'view_delivery_settings',
                        'view_brand_delivery_settings',
                        'view_notification_templates',
                        'check_for_updates',
                        'view_brand_privacy_settings',
                        'view_custom_fields',
                        'view_notification_settings'
                    ]) && $this->checkAppType(),
                    'icon' => 'settings',
                    'subMenu' => [
                        [
                            'name' => __t('app').' '.__t('settings'),
                            'url' => route('settings.index'),
                            'icon' => 'user',
                            'permission' => authorize_any([
                                'view_settings',
                                'view_notification_settings',
                                'check_for_updates',
                                'view_delivery_settings'
                            ])
                        ],
                        [
                            'name' => __t('brand').' '.__t('settings'),
                            'url' => route('settings.brand'),
                            'icon' => 'user',
                            'permission' => authorize_any([
                                'view_brand_delivery_settings',
                                'view_templates',
                                'view_brand_privacy_settings',
                                'view_custom_fields',
                                'view_notification_settings'
                            ])
                        ],
                        [
                            'name' => __t('brand').' '.__t('templates'),
                            'url' => route('settings.templates'),
                            'icon' => 'user',
                            'permission' => authorize_any([
                                'view_brand_delivery_settings'
                            ])
                        ],
                    ],
                ],
            ],
            'settings' => resolve(SettingsService::class)->getCachedFormattedSettings(),
            'top_bar_menu' => [
                [
                    'optionName' => __t('my_profile'),
                    'optionIcon' => 'user',
                    'url' => brand() ? request()->root().'/admin/'.brand()->short_name.'/my-profile' : request()->root().'/admin/my-profile'
                ],
                [
                    'optionName' => __t('notifications'),
                    'optionIcon' => 'bell',
                    'url' => brand() ? route('tenant.notification.index', ['brand_dashboard' => brand()->short_name]) : route("notifications.index")
                ],
                [
                    'optionName' => __t('settings'),
                    'optionIcon' => 'settings',
                    'url' => authorize_any([
                        'view_settings',
                        'view_corn_job_settings',
                        'view_delivery_settings',
                        'view_notification_settings'
                    ]) ? request()->root().'/admin/settings' : '#'
                ],
                [
                    'optionName' => __t('log_out'),
                    'optionIcon' => 'log-out',
                    'url' => request()->root().'/admin/log-out'
                ],
            ],
            'logo' => empty(config('settings.application.company_logo')) ? request()->root().'/images/logo.png' : request()->root().config('settings.application.company_logo'),
            'logo_icon' => empty(config('settings.application.company_icon')) ? request()->root().'/images/icon.png' : request()->root().config('settings.application.company_icon')
        ]);
    }
}
