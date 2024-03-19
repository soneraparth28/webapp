<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Helpers\Traits\BrandInactiveTrait;
use App\Hooks\HookContract;

class BeforeUserUpdated extends HookContract
{
    use InstanceCreator, BrandInactiveTrait;

    public function handle()
    {
        $this->actionIfInactive();
    }
}