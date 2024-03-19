<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Services\Core\Auth\UserInvitationService;
use App\Services\Core\Auth\UserService;

class AfterInvitationCanceled extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
//        resolve(UserInvitationService::class)
//            ->setModel($this->model)
//            ->delete();
    }
}