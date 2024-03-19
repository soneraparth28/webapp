<?php
// define your route here
// Check core/app.php for info
// all of your route will start with the prefix brands/{brand_id or brand_short_name}
// You will get the brand_id by from request with the key of brand_id

use App\Http\Controllers\{
    Campaign\CampaignCountController,
    CustomField\CustomFieldController,
    Dashboard\EmailStatisticsController,
    Email\EmailLogController,
    Lists\ListController,
    Lists\ListSubscriberController,
    Subscriber\BlacklistSubscriberController,
    Subscriber\ImportSubscriberController,
    Subscriber\SubscriberController,
    Subscriber\SubscriberCountController,
    Segment\SegmentCountController,
    Template\TemplateController};
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'subscribers', 'as' => 'subscribers.'], function (Router $router) {

    $router->get('import', [ImportSubscriberController::class, 'create'])
        ->name('import');

    $router->post('add-to-lists', [ListSubscriberController::class, 'store'])
        ->name('add-to-lists');

    $router->post('remove-from-lists', [ListSubscriberController::class, 'destroy'])
        ->name('remove-from-lists');

    $router->post('add-to-blacklist', [BlacklistSubscriberController::class, 'store'])
        ->name('add-to-blacklist');

    $router->post('remove-from-blacklist', [BlacklistSubscriberController::class, 'update'])
        ->name('remove-from-blacklist');

    $router->post('view-imported', [ImportSubscriberController::class, 'index'])
        ->name('view-imported');

    $router->post('bulk-import', [ImportSubscriberController::class, 'update'])
        ->name('bulk-import');

    $router->post('quick-import', [ImportSubscriberController::class, 'store'])
        ->name('quick-import');

});

Route::resource('subscribers', SubscriberController::class);


Route::group(['prefix' => 'lists', 'as' => 'lists.'], function (Router $router) {

    $router->get('{list}/subscribers', [ListSubscriberController::class, 'show'])
        ->name('dynamic-subscribers');
});

Route::resource('lists', ListController::class);

Route::group(['prefix' => 'email-logs', 'as' => 'emails.'], function (Router $router) {

    Route::get('/', [EmailLogController::class, 'index'])
        ->name('index');

    Route::delete('{emailLog}', [EmailLogController::class, 'destroy'])
        ->name('destroy');
});

Route::resource('templates', TemplateController::class);

Route::get('subscribers-count/{range_type?}', [SubscriberCountController::class, 'index'])
    ->name('brands.subscribers-count');

Route::get('segments-count', [SegmentCountController::class, 'index'])
    ->name('brand-segment-counts.index');

Route::get('campaigns-count/{last24Hours?}', [CampaignCountController::class, 'index'])
    ->name('brands.campaigns_count');

Route::get('email-statistics/{range_type?}', [EmailStatisticsController::class, 'show'])
    ->name('brands.email-statistics');

Route::get('custom-field/{context?}', [CustomFieldController::class, 'index'])
    ->name('custom-fields.lists');
