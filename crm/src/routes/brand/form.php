<?php
/**
 * This route only for view form
 * Don't write other route here
 * Trina keep things clean :)
 */

use App\Http\Controllers\Brand\BrandDashboardNavigationController;
use App\Http\Controllers\Brand\BrandUserController;
use App\Http\Controllers\Core\Auth\Role\RoleController as BaseRoleController;
use App\Http\Controllers\Core\Auth\User\UserRoleController;
use App\Http\Controllers\Core\Auth\UserInvitationController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Segment\DuplicateSegmentController;
use Illuminate\Routing\Router;

Route::get('campaign/list', [BrandDashboardNavigationController::class, 'campaigns'])
    ->name('campaigns.lists');

Route::get('email/list', [BrandDashboardNavigationController::class, 'emails'])
    ->name('emails.lists');

Route::get('list/page', [BrandDashboardNavigationController::class, 'list'])
    ->name('lists.lists');

Route::get('segment/list', [BrandDashboardNavigationController::class, 'segments'])
    ->name('segments.lists');

Route::get('segments/{segment}/copy', [DuplicateSegmentController::class, 'show'])
    ->name('segments.copy');

Route::get('subscriber/list', [BrandDashboardNavigationController::class, 'subscribers'])
    ->name('subscribers.lists');

Route::get('black-listed-subscriber/list', [BrandDashboardNavigationController::class, 'blockedSubscribers'])
    ->name('subscribers-black-lists.lists');

Route::get('template-card-view/list', [BrandDashboardNavigationController::class, 'templatesCardView'])
    ->name('templates.lists');

Route::group(['prefix' => 'roles', 'as' => 'roles.'], function (Router $router) {

    $router->get('/', [BaseRoleController::class, 'index'])
        ->name('index');

    $router->get('select', [RoleController::class, 'list'])
        ->name('lists');

    $router->post('{role}/attach-users', [UserRoleController::class, 'attachUsers'])
        ->name('attach_users_to');

});

Route::resource('roles', RoleController::class);

Route::group(['prefix' => 'users', 'as' => 'users.'], function (Router $router) {

    $router->post('attach-roles/{user}', [UserRoleController::class, 'store'])
        ->name('attach-roles');

    $router->post('detach-roles/{user}', [UserRoleController::class, 'update'])
        ->name('detach-roles');

});

Route::resource('users', BrandUserController::class);

Route::post('users/invite-user', [UserInvitationController::class, 'invite'])
    ->name('user.invite');

Route::post('users/cancel-invitation/{user}', [UserInvitationController::class, 'cancel'])
    ->name('invitation.cancel-user');





