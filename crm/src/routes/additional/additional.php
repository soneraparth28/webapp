<?php

use App\Http\Controllers\Brand\BrandDashboardNavigationController;
use App\Http\Controllers\Brand\BrandDeliverySettingController;
use App\Http\Controllers\Campaign\CampaignAPIController;
use App\Http\Controllers\Campaign\CampaignEmailStatisticsController;
use App\Http\Controllers\Campaign\CampaignTestController;
use App\Http\Controllers\Email\EmailLogController;
use App\Http\Controllers\Email\TestMailController;
use App\Http\Controllers\Lists\ListAPIController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Role\PermissionController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Segment\SegmentLogicCountController;
use App\Http\Controllers\Segment\SegmentSubscriberController;
use App\Http\Controllers\SES\SnsSubscriptionController;
use App\Http\Controllers\Settings\DeliveryAPIController;
use App\Http\Controllers\Settings\DeliveryQuotaController;
use App\Http\Controllers\Subscriber\SubscriberAPIController;
use App\Http\Controllers\Template\TemplateAPIController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('users/list', [UserController::class, 'lists'])
    ->name('users.lists');


Route::group(['prefix' => 'settings'], function () {
    Route::get('delivery/{context?}', [BrandDeliverySettingController::class, 'index'])
        ->name('delivery-settings.index')
        ->middleware('can:view_specific_brand_delivery_settings');

    Route::post('delivery', [BrandDeliverySettingController::class, 'update'])
        ->name('delivery-settings.update')
        ->middleware('can:update_specific_brand_delivery_settings');

    Route::delete('delivery/delete', [BrandDeliverySettingController::class, 'delete'])
        ->name('delivery-settings.delete')
        ->middleware('can:update_specific_brand_delivery_settings');
});

Route::post('test-mail/send', [TestMailController::class, 'send'])
    ->name('brand-test-mail.send')
    ->middleware(['can:update_specific_brand_delivery_settings']);

Route::get('subscribers/select', [SubscriberAPIController::class, 'view'])
    ->middleware('can:view_subscribers')
    ->name('subscribers.select');

Route::get('lists/select', [ListAPIController::class, 'index'])
    ->middleware('can:view_lists')
    ->name('lists.select');

Route::get('campaigns/select', [CampaignAPIController::class, 'index'])
    ->middleware('can:view_campaigns')
    ->name('campaigns.select');

Route::get('templates/select', [TemplateAPIController::class, 'index'])
    ->middleware('can:view_templates')
    ->name('templates.select');

Route::get('lists/{lists}/subscribers/count', [ListAPIController::class, 'count'])
    ->middleware('can:view_lists')
    ->name('lists.subscriber_count');

Route::get('campaigns/{campaign}/subscribers/count', [CampaignAPIController::class, 'count'])
    ->middleware('can:view_campaigns')
    ->name('campaigns.subscriber_count');

Route::get('campaigns/{campaign}/email-logs/rates', [CampaignAPIController::class, 'rates'])
    ->middleware('can:view_campaigns')
    ->name('campaigns.rates');

Route::get('lists/{lists}/view', [ListAPIController::class, 'view'])
    ->middleware('can:view_lists')
    ->name('lists.view');

Route::get('campaigns/{campaign}/view', [CampaignAPIController::class, 'view'])
    ->middleware('can:view_campaigns')
    ->name('campaigns.view');

Route::get('lists/{id}/copy', [ListAPIController::class, 'copy'])
    ->middleware('can:view_lists')
    ->name('lists.copy');

Route::post('campaigns/{campaign}/test', [CampaignTestController::class, 'test'])
    ->middleware('can:test_campaign')
    ->name('campaign.test');

Route::get('templates/{template}/content', [TemplateAPIController::class, 'body'])
    ->name('templates.content')
    ->middleware('can:view_templates');

Route::get('templates/{template}/copy', [TemplateAPIController::class, 'copy'])
    ->name('templates.copy')
    ->middleware('can:create_templates');

Route::get('sns-subscriptions', [SnsSubscriptionController::class, 'index'])
    ->name('sns.subscription');

Route::post('sns-subscriptions/confirm', [SnsSubscriptionController::class, 'confirm'])
    ->name('sns.confirm_subscription')
    ->middleware('can:confirm_sns_subscription');

Route::get('campaigns/{campaign}/email-statistics/{range_type?}', [CampaignEmailStatisticsController::class, 'show'])
    ->middleware('can:view_campaigns');

Route::get('segments/{segment}/subscribers/count', [SegmentSubscriberController::class, 'show'])
    ->middleware('can:view_segments');

Route::get('settings/page', [BrandDashboardNavigationController::class, 'settings'])
    ->name('settings.lists');

Route::post('subscribers/bulk-destroy', [SubscriberAPIController::class, 'bulkDestroy'])
    ->name('subscribers.bulk-destroy');

Route::post('subscribers/update-status', [SubscriberAPIController::class, 'updateStatus'])
    ->name('subscribers.update-status');

Route::get('segments/segment-rules-count', [SegmentLogicCountController::class, 'index'])
    ->middleware('can:view_segments');

Route::get('template-list-view/list', [BrandDashboardNavigationController::class, 'templatesListView'])
    ->middleware('can:view_templates');

Route::get('/email-logs/{emailLog}', [EmailLogController::class, 'show'])
    ->middleware('can:view_emails');

Route::get('/notifications/list', [NotificationController::class, 'list'])
    ->name('notification.index');

Route::get('permissions', [PermissionController::class, 'index'])
    ->middleware('can:view_roles')
    ->name('permission.index');

Route::get('selectable-users', [UserController::class, 'brandUsers'])
    ->middleware('can:view_users');

Route::get('selectable-roles', [RoleController::class, 'brandRoles'])
    ->middleware('can:view_roles');

Route::get('list/{id}/view', [BrandDashboardNavigationController::class, 'listView'])
    ->name('list.show-page')
    ->middleware('can:view_lists');

Route::get('get-subscriber-api-url/{regenerate?}', [SubscriberAPIController::class, 'getApiUrl'])
    ->name('subscriber.url-generator')
    ->middleware('can:generate_subscriber_api_url');

Route::get('delivery-quotas', [DeliveryQuotaController::class, 'show'])
    ->name('delivery-settings.quotas');

Route::get('delivery/provider', [DeliveryAPIController::class, 'show'])
    ->name('delivery-settings.provider');

Route::get('check-mail-settings', [DeliveryAPIController::class, 'isExists'])
    ->name('check-brand-mail-settings');
