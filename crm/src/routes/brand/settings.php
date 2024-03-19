<?php

use App\Http\Controllers\Brand\BrandCustomFieldController;
use App\Http\Controllers\Brand\BrandDeliverySettingController;
use App\Http\Controllers\Brand\BrandNotificationEventController;
use App\Http\Controllers\Brand\BrandNotificationTemplateController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'settings'], function (Router  $router) {
    $router->resource('custom-fields', BrandCustomFieldController::class)
        ->except('edit');
});

Route::group(['prefix' => 'notification-settings', 'as' => 'notification-settings.'], function (Router $router) {

    $router->get('/', [BrandNotificationEventController::class, 'index'])
        ->name('index');

    $router->get('{notification_event}', [BrandNotificationEventController::class, 'show'])
        ->name('show');

    $router->patch('{notification_event}', [BrandNotificationEventController::class, 'update'])
        ->name('update');;
});

Route::patch('notification-templates/{notification_template}', [BrandNotificationTemplateController::class, 'update'])
    ->name('notification-templates.update');


