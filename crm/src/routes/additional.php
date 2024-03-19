<?php

use App\Http\Controllers\{Brand\BrandController as AppBrandController,
    Brand\BrandStatusController,
    Campaign\CampaignCountController,
    Core\Auth\Role\PermissionController,
    Core\Auth\User\AuthenticateUserController,
    Core\Auth\User\UserController,
    Core\Brand\BrandController,
    Core\Auth\User\LoginController,
    Core\Auth\User\UserPasswordController as BaseUserPasswordControllerAlias,
    Core\Brand\BrandDashboardRedirect,
    Core\Brand\BrandDeliverySettingController,
    Core\Brand\BrandGroupController,
    Core\Brand\BrandShortNameGeneratorController,
    Core\Brand\UserBrandController,
    Core\Auth\User\UserUpdateController,
    Core\Auth\User\UserThumbnailController,
    Core\Builder\Form\CustomFieldTypeController,
    Core\LanguageController,
    Core\Log\ActivityLogController,
    Core\Notification\NotificationChannelController,
    Core\Notification\NotificationController,
    Core\Notification\NotificationEventController,
    Email\TestMailController,
    Notification\NotificationController as AppNotificationController,
    Notification\NotificationEventController as AppNotificationEventController,
    Core\Setting\StatusController,
    Core\Setting\TypeController,
    Dashboard\EmailStatisticsController,
    Notification\NotificationSettingController,
    Role\RoleController,
    Segment\LogicController,
    Segment\OperatorController,
    SES\SnsSubscriptionController,
    Settings\CronJobSettingController,
    Settings\DeliveryAPIController,
    Settings\DeliveryQuotaController,
    Settings\SettingApiController,
    Settings\SettingViewController,
    SMTP\SMTPTrackerController,
    Status\StatusAPIController,
    Brand\TotalBrandController,
    Subscriber\SubscriberCountController,
    Template\TemplateAPIController,
    User\UserController as AppUserController};
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/brand/dashboard/redirect/{params?}', [BrandDashboardRedirect::class, 'redirect'])
    ->name('redirect.brand_dashboard');

Route::get('brands/generate-short-names', BrandShortNameGeneratorController::class)
    ->name('brands.generate_short_names');

Route::group(['prefix' => 'app'], function (Router $router) {
    $router->get('types', [TypeController::class, 'index'])
        ->name('types.index');

    $router->get('statuses', [StatusController::class, 'index'])
        ->name('statuses.index');

    $router->group(['prefix' => 'notification-events', 'as' => 'notification-events.'], function (Router $router) {

        $router->get('/', [AppNotificationEventController::class, 'index'])
            ->name('index');

        $router->get('{notification_event}', [NotificationEventController::class, 'show'])
            ->name('show');
    });


    $router->get('notification-channels', [NotificationChannelController::class, 'index'])
        ->name('notification-channels.index');

    $router->get('custom-field-types', [CustomFieldTypeController::class, 'index']);

    $router->get('templates/{template}/content', [TemplateAPIController::class, 'body'])
        ->middleware('can:view_templates')
        ->name('template.body');

    $router->post('brands/{brand}/update-status', [BrandStatusController::class, 'update'])
        ->middleware('can:update_brands')
        ->name('brands.update-status');

    $router->get('templates/{template}/copy', [TemplateAPIController::class, 'copy'])
        ->middleware('can:view_templates')
        ->name('templates.copy');

    $router->get('subscribers-count/{range_type?}', [SubscriberCountController::class, 'index'])
        ->name('app.subscribers-count')
        ->middleware('can:subscribers_count_app');

    $router->get('campaigns-count/{last24Hours?}', [CampaignCountController::class, 'index'])
        ->name('app.campaigns_count')
        ->middleware('can:campaigns_count_app');

    $router->get('email-statistics/{range_type?}', [EmailStatisticsController::class, 'show'])
        ->name('app.email-statistics')
        ->middleware('can:email_statistics_app');


    $router->get('delivery-quotas', [DeliveryQuotaController::class, 'show'])
        ->name('delivery-settings.quotas');

    $router->get('delivery/provider', [DeliveryAPIController::class, 'show'])
        ->name('delivery-settings.provider');

    $router->post('test-mail/send', [TestMailController::class, 'send'])
        ->name('test-mail.send')
        ->middleware(['can:update_delivery_settings']);

});

Route::get('auth/permissions', [PermissionController::class, 'index'])
    ->middleware(['can:view_roles', 'app.role'])
    ->name('permissions.index');


Route::get('settings-format', [SettingApiController::class, 'configs'])->name('settings.config');

Route::get('brands-group/list', [BrandGroupController::class, 'list'])
    ->name('brand-group.lists');

Route::get('brands/list', [BrandController::class, 'list'])
    ->middleware('can:view_brands')
    ->name('brands.lists');

Route::get('users/list', [UserController::class, 'list'])
    ->name('users.lists');

Route::get('settings', [SettingViewController::class, 'appSettings'])
    ->name('settings.index');

Route::get('brand/settings', [SettingViewController::class, 'brandSettings'])
    ->name('settings.brand');

Route::get('brand/templates', [SettingViewController::class, 'templateSettings'])
    ->middleware('can:view_templates', 'app.role')
    ->name('settings.templates');


Route::get('log-out', [LoginController::class, 'logOut'])
    ->name('auth.log_out');

Route::get('user/notifications', [NotificationController::class, 'index'])
    ->name('user-notifications.index');

Route::post('user/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])
    ->name('user-notifications.mark-as-read');

Route::post('user/notifications/mark-all-as-read', [NotificationController::class, 'markAsReadAll'])
    ->name('user-notifications.mark-all-as-read');

Route::post('user/notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])
    ->name('user-notifications.mark-as-unread');

Route::get('notification-events/settings/{notification_event}', [NotificationSettingController::class, 'showSettings'])
    ->name('notification_events.settings');

Route::get('statuses/{type}', [StatusAPIController::class, 'index'])->name('statuses.list');

Route::get('brands-count', [TotalBrandController::class, 'index'])
    ->name('brands.count');

Route::get('users', [AppUserController::class, 'index'])
    ->name('users.view')
    ->middleware(['can:view_users', 'app.role']);

Route::get('roles', [RoleController::class, 'list'])
    ->name('roles.view')
    ->middleware(['can:view_roles', 'app.role']);

Route::get('all-roles', [RoleController::class, 'roles'])
    ->name('roles.lists')
    ->middleware(['can:view_roles', 'app.role']);

Route::get('brand/settings/delivery/{provider?}', [BrandDeliverySettingController::class, 'index'])
    ->middleware('can:view_delivery_settings');;

Route::get('user/activities/{user}', [ActivityLogController::class, 'activities'])
    ->name('user.activities')
    ->middleware('can:view_activities');

Route::get('authenticate-user', [AuthenticateUserController::class, 'show'])
    ->name('user.authenticate-user');

Route::post('auth/users/change-settings', [UserUpdateController::class, 'update'])
    ->name('users.change-settings');

Route::get('{brand_id}/my-profile', [AppUserController::class, 'profile']);

Route::get('my-profile', [AppUserController::class, 'user'])
    ->name('user.profile');

Route::get('logic-names', [LogicController::class, 'index'])
    ->name('logic.names');

Route::get('logic-operators', [OperatorController::class, 'index'])
    ->name('logic.operators');

Route::get('user/activity-log', [ActivityLogController::class, 'show'])
    ->name('activity-log.show');

Route::get('brands', [AppBrandController::class, 'index'])
    ->name('brands.index')
    ->middleware('can:view_brands');

Route::get('user/brands', [UserBrandController::class, 'index'])
    ->name('users.brand-list');

Route::get('sns-subscriptions', [SnsSubscriptionController::class, 'index'])
    ->name('sns.subscriptions');

Route::post('sns-subscriptions/confirm', [SnsSubscriptionController::class, 'confirm'])
    ->name('sns-subscriptions.confirm');

Route::get('notifications/list', [AppNotificationController::class, 'index'])
    ->name('notifications.index');

Route::post('auth/users/profile-picture', [UserThumbnailController::class, 'store'])
    ->name('users.change-profile-picture');

Route::group(['prefix' => 'auth/users/{user}'], function (Router  $router) {
    $router->post('password/change', [BaseUserPasswordControllerAlias::class, 'update'])
        ->name('users.change-password');
});

Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');

Route::get('app/check-mail-settings', [DeliveryAPIController::class, 'isExists'])
    ->name('check-mail-settings');

Route::get('app/settings/cronjob', [CronJobSettingController::class, 'index'])
    ->name('cron-job-settings');