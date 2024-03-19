<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class BeforeUserDeleted extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        if ($this->model->isAppAdmin() && !$this->model->isInvited())
            throw new GeneralException(trans('default.action_not_allowed'));

        if ($this->model->id == auth()->id())
            throw new GeneralException(trans('default.cant_delete_own_account'));

        $this->model->forceDelete();
    }
}