<?php


use App\Http\Controllers\Core\{Brand\BrandDeliverySettingController,
    Brand\BrandGroupBrandController,
    Brand\BrandGroupController,
    Brand\BrandPrivacySettingController,
    Brand\BrandSettingController,
    Brand\BrandUserController,
    Builder\Template\TemplateController,
    Log\ActivityLogController,
    Notification\NotificationEventTemplateController,
    Notification\NotificationTemplateController,
    Brand\BrandController,
    Builder\Form\CustomFieldController,
    Setting\CornJobSettingController,
    Setting\DeliverySettingController,
    Setting\NotificationSettingController,
    Setting\SettingController};

Route::group(['prefix' => 'app'], function () {
    Route::resource('brands', BrandController::class);

    Route::get('settings', [SettingController::class, 'index'])
        ->name('settings.index');

    Route::post('settings', [SettingController::class, 'update'])
        ->name('settings.update');

    Route::get('settings/delivery-settings', [DeliverySettingController::class, 'index'])
        ->name('settings.view-delivery');

    Route::post('settings/delivery-settings', [DeliverySettingController::class, 'update'])
        ->name('settings.update-delivery');

    Route::get('settings/delivery-settings/{provider}', [DeliverySettingController::class, 'show'])
        ->name('settings.view_delivery');

    Route::post('settings/brand-delivery', [BrandDeliverySettingController::class, 'update'])
        ->name('settings.update-brand-delivery');

    Route::get('settings/brand-privacy', [BrandPrivacySettingController::class, 'index'])
        ->name('settings.view-brand-privacy');

    Route::post('settings/brand-privacy', [BrandPrivacySettingController::class, 'update'])
        ->name('settings.update-brand-privacy');

    Route::get('settings/corn-job-settings', [CornJobSettingController::class, 'index'])
        ->name('settings.view-corn-job');

    Route::post('settings/corn-job-settings', [CornJobSettingController::class, 'update'])
        ->name('settings.update-corn-job');

    Route::resource('custom-fields', CustomFieldController::class);
    Route::resource('notification-settings', NotificationSettingController::class)
        ->only('show', 'update');

    Route::get('brand-groups/brands/{brand_group}', [BrandGroupBrandController::class, 'show'])
        ->name('brand-groups.view_brands');

    Route::post('brand-groups/attach-brand/{brand_group}', [BrandGroupBrandController::class, 'store'])
        ->name('brand-groups.attach-brand');

    Route::post('brand-groups/detach-brand', [BrandGroupBrandController::class, 'destroy'])
        ->name('brand-groups.detach-brand');

    Route::resource('brand-groups', BrandGroupController::class);

    Route::resource('notification-templates', NotificationTemplateController::class)
        ->except('create', 'edit');

    Route::post('notification-events/{event}/attach-templates', [NotificationEventTemplateController::class, 'store'])
        ->name('notification-events.attach-templates');

    Route::post('notification-events/{event}/detach-templates', [NotificationEventTemplateController::class, 'update'])
    ->name('notification-events.detach-templates');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    Route::post('brands/{brand}/attach-users', [BrandUserController::class, 'store'])
        ->name('brands.attach-users');

    Route::post('brands/{brand}/detach-users', [BrandUserController::class, 'update'])
        ->name('brands.detach-users');

    Route::get('brands/{brand}/users', [BrandUserController::class, 'index'])
        ->name('brands.user-list');

    Route::post('brands/{brand}/settings', [BrandSettingController::class, 'update'])
        ->name('brands.update-settings');

    Route::resource('templates', TemplateController::class);


});
