<?php


use App\Http\Controllers\Segment\DuplicateSegmentController;
use App\Http\Controllers\Segment\SegmentController;
use Illuminate\Routing\Router;

Route::group(['prefix' => 'segments', 'as' => 'segments.'], function (Router $router) {

    $router->get('select', [SegmentController::class, 'view'])
        ->name('view');

    $router->post('{segment}/duplicate', [DuplicateSegmentController::class, 'store'])
        ->name('duplicate-segment');
});

Route::resource('segments', SegmentController::class);




