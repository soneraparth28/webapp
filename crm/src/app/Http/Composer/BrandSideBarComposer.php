<?php


namespace App\Http\Composer;


use App\Helpers\Traits\HasAppRole;
use App\Services\Settings\SettingsService;
use Illuminate\View\View;

class BrandSideBarComposer
{
    public function compose(View $view)
    {
        $view->with([
            'permissions' => [
                [
                    'name' => __t('dashboard'),
                    'url' => route('tenant.brand-dashboard', ['brand_dashboard' => brand()->short_name]),
                    'icon' => 'pie-chart',
                    'permission' => auth()->user()->can('manage_dashboard')
                ],
                [
                    'name' => __t('campaigns'),
                    'id' => 'campaigns-1',
                    'icon' => 'sun',
                    'permission' => auth()->user()->can('view_campaigns'),
                    'subMenu' => [
                        [
                            'name' => __t('all_feature_name', ['name' => 'campaigns']),
                            'url' => route('tenant.campaigns.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'list',
                            'permission' => auth()->user()->can('view_campaigns')
                        ],
                        [
                            'name' => __t('add_feature_name', ['name' => 'campaign']),
                            'url' => route('tenant.campaigns.create', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'list',
                            'permission' => auth()->user()->can('create_campaigns')
                        ],
                    ]
                ],
                [
                    'name' => __t('emails'),
                    'url' => route('tenant.emails.lists', ['brand_dashboard' => brand()->short_name]),
                    'icon' => 'mail',
                    'permission' => auth()->user()->can('view_emails')
                ],
                [
                    'name' => __t('lists'),
                    'id' => 'lists-1',
                    'icon' => 'menu',
                    'permission' => auth()->user()->can('view_lists') || auth()->user()->can('view_segments'),
                    'subMenu' => [
                        [
                            'name' => __t('all_feature_name', ['name' => 'lists']),
                            'url' => route('tenant.lists.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'list',
                            'permission' => auth()->user()->can('view_lists')
                        ],
                        [
                            'name' => __t('add_feature_name', ['name' => 'list']),
                            'url' => route('tenant.lists.create', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'list',
                            'permission' => auth()->user()->can('create_lists')
                        ],
                        [
                            'name' => __t('segments'),
                            'url' => route('tenant.segments.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'key',
                            'permission' => auth()->user()->can('create_segments')
                        ],
                        [
                            'name' => __t('add_feature_name', ['name' => 'segment']),
                            'url' => route('tenant.segments.create', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'key',
                            'permission' => auth()->user()->can('view_segments')
                        ],
                    ]
                ],
                [
                    'name' => __t('subscribers'),
                    'id' => 'subscribers',
                    'icon' => 'message-circle',
                    'permission' => auth()->user()->can('view_subscribers') || auth()->user()->can('view_subscribers'),
                    'subMenu' => [
                        [
                            'name' => __t('all_feature_name', ['name' => 'subscribers']),
                            'url' => route('tenant.subscribers.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'message-square',
                            'permission' => auth()->user()->can('view_subscribers')
                        ],
                        [
                            'name' => __t('add_feature_name', ['name' => 'subscriber']),
                            'url' => route('tenant.subscribers.create', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'message-square',
                            'permission' => auth()->user()->can('create_subscribers')
                        ],
                        [
                            'name' => __t('black_list'),
                            'url' => route('tenant.subscribers-black-lists.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'lock',
                            'permission' => auth()->user()->can('view_subscribers_black_lists')
                        ],
                    ]
                ],
                [
                    'name' => __t('templates'),
                    'id' => 'templates-1',
                    'icon' => 'cpu',
                    'permission' => auth()->user()->can('view_templates'),
                    'subMenu' => [
                        [
                            'name' => __t('card_view'),
                            'url' => route('tenant.templates.lists', ['brand_dashboard' => brand()->short_name]),
                            'icon' => 'align-center',
                            'permission' => auth()->user()->can('view_templates')
                        ],
                        [
                            'name' => __t('list_view'),
                            'url' => request()->root().'/brands/'.brand()->short_name.'/template-list-view/list',
                            'icon' => 'align-justify',
                            'permission' => auth()->user()->can('view_templates')
                        ],
                        [
                            'name' => __t('add_feature_name', ['name' => 'template']),
                            'url' => request()->root().'/brands/'.brand()->short_name.'/templates/create',
                            'icon' => 'align-justify',
                            'permission' => auth()->user()->can('view_templates')
                        ],
                    ]
                ],
                [
                    'name' => __t('users_roles'),
                    'url' => request()->root().'/brands/' . brand()->short_name . '/users/list',
                    'icon' => 'user-check',
                    'permission' => authorize_any(['view_users', 'view_roles'])
                ],
                [
                    'name' => __t('settings'),
                    'url' => route('tenant.settings.lists', ['brand_dashboard' => brand()->short_name]),
                    'permission' => authorize_any([
                        'view_delivery_settings',
                        'view_custom_fields',
                        'view_notification_settings',
                        'generate_subscriber_api_url'
                    ]),
                    'icon' => 'settings',
                ]
            ],
            'settings' => resolve(SettingsService::class)->getCachedFormattedSettings(),
            'top_bar_menu' => array_filter([
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
                (new HasAppRole())->hasAppRole() && authorize_any([
                    'view_settings',
                    'view_corn_job_settings',
                    'view_delivery_settings',
                    'view_notification_settings'
                ]) ?
                    [
                        'optionName' => __t('settings'),
                        'optionIcon' => 'settings',
                        'url' => authorize_any([
                            'view_settings',
                            'view_corn_job_settings',
                            'view_delivery_settings',
                            'view_notification_settings'
                        ]) ? request()->root().'/admin/settings' : '#'
                    ] : [],
                [
                    'optionName' => __t('log_out'),
                    'optionIcon' => 'log-out',
                    'url' => request()->root().'/admin/log-out'
                ]
            ], function ($ar) {
                return count($ar);
            }),
            'logo' => empty(config('settings.application.company_logo')) ? request()->root().'/images/logo.png' : request()->root().config('settings.application.company_logo'),
            'logo_icon' => empty(config('settings.application.company_icon')) ? request()->root().'/images/icon.png' : request()->root().config('settings.application.company_icon')
        ]);
    }
}
