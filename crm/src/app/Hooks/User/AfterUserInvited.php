<?php


namespace App\Hooks\User;


use App\Hooks\HookContract;
use App\Jobs\User\UserInvited;

class AfterUserInvited extends HookContract
{

    public function handle()
    {
        UserInvited::dispatchNow($this->model);
    }
}
