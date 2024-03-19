<?php

use App\Http\Controllers\Campaign\{CampaignAudienceController,
    CampaignConfirmationController,
    CampaignController,
    CampaignDeliveryController,
    CampaignDuplicateController,
    CampaignEmailLogController,
    CampaignSubscriberController,
    CampaignTemplateController};
use Illuminate\Routing\Router;

Route::group(['prefix' => 'campaigns', 'as' => 'campaigns.'], function (Router $router) {

    $router->post('{campaign}/delivery-settings', [CampaignDeliveryController::class, 'store'])
        ->name('delivery-settings');

    $router->post('{campaign}/audiences', [CampaignAudienceController::class, 'store'])
        ->name('audiences');

    $router->post('{campaign}/template', [CampaignTemplateController::class, 'store'])
        ->name('template');

    $router->post('{campaign}/confirm', [CampaignConfirmationController::class, 'store'])
        ->name('confirm');

    $router->get('{campaign}/subscribers', [CampaignSubscriberController::class, 'index'])
        ->name('subscribers');

    $router->get('{campaign}/email-logs', [CampaignEmailLogController::class, 'show'])
        ->name('email-logs');

    $router->post('{campaign}/duplicate', [CampaignDuplicateController::class, 'duplicate'])
        ->name('duplicate');

    $router->get('{campaign}/change-status', [CampaignController::class, 'changeCurrentStatus'])
        ->name('pause-or-resume');
});

Route::resource('campaigns', CampaignController::class);