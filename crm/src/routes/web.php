<?php

use App\Http\Controllers\Artisan\QueueActionController;
use App\Http\Controllers\Artisan\ScheduleActionController;
use App\Http\Controllers\Core\LanguageController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\InstallDemoDataController;
use App\Http\Controllers\Subscriber\SubscriberAPIController;
use App\Http\Controllers\Subscriber\SubscriberStatusController;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Support\Facades\Route;

//Redirect route
Route::redirect('/', url('admin/users/login'));

// Switch between the included languages
Route::get('languages/{lang}', [LanguageController::class, 'show'])->name('languages.show');
Route::get('lang/{lang}', [LanguageController::class, 'swap'])->name('language.change');

Route::group(['prefix' => 'webhook'], function () {
    include_route_files(__DIR__ . '/webhook/');
});
/*
 * All login related route will be go there
 * Only guest user can access this route
 */

Route::group(['middleware' => ['guest', 'app.installed'], 'prefix' => 'users'], function () {
    include_route_files(__DIR__ . '/user/');
});

Route::group(['middleware' => ['guest', 'app.installed'], 'prefix' => 'admin/users'], function () {
    include_route_files(__DIR__ . '/login/');
});

/**
 * This route is only for brand redirection
 * And for some additional route
 */
Route::group(['prefix' => 'admin', 'middleware' => ['app.installed', 'auth', 'authorize']], function () {
    include __DIR__ . '/additional.php';
});

Route::group(['prefix' => 'brands/{brand_dashboard}', 'as' => 'tenant.', 'middleware' => ['app.installed', 'auth', 'authorize', 'brand']], function () {
    include_route_files(__DIR__ . '/additional/');
});

/**
 * You have to define your brand route here
 * But don't change anything in core group
 * And most probably you are not gonna need that
 * cause it's already in brand folder.
 * You can create any file with any name you want
 * And can assign any route on this file
 * brand_dashboard has been bypassed in @var PermissionMiddleware
 */

Route::group(['prefix' => 'brands/{brand_dashboard}', 'as' => 'tenant.', 'middleware' => 'admin'], function () {
    include_route_files(__DIR__ . '/brand/');
});

/**
 * Backend Routes
 * Namespaces indicate folder structure
 * All your route in sub file must have a name with not more than 2 index
 * Example: brand.index or dashboard
 * See @var PermissionMiddleware for more information
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'as' => 'core.'], function () {

    /*
     * (good if you want to allow more than one group in the core,
     * then limit the core features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     * These routes can not be hit if the password is expired
     */
    include_route_files(__DIR__ . '/core/');

});


Route::get('unsubscribe/{brand_id}/{email}', [SubscriberStatusController::class, 'unsubscribe'])
    ->name('subscriber.unsubscribe')
    ->middleware('signed');

Route::post('api/subscriber/{brand}/store', [SubscriberAPIController::class, 'store'])
    ->name('subscriber-external-api');

Route::post('api/subscriber/{brand}/update', [SubscriberAPIController::class, 'update'])
    ->name('subscriber-update-api');

Route::any('install-demo-data', [InstallDemoDataController::class, 'run'])
    ->name('install-demo-data');

Route::group(['prefix' => 'actions', 'as' => 'actions.'], function () {

    Route::any('queue/{queue}', [QueueActionController::class, 'run'])
        ->name('queue');

    Route::any('run-scheduler', [ScheduleActionController::class, 'run'])
        ->name('scheduler');
});
