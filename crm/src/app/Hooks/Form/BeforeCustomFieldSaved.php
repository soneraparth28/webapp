<?php


namespace App\Hooks\Form;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Helpers\Traits\BrandInactiveTrait;
use App\Hooks\HookContract;

class BeforeCustomFieldSaved extends HookContract
{
    use InstanceCreator, BrandInactiveTrait;

    public function handle()
    {
        $this->actionIfInactive();
    }
}