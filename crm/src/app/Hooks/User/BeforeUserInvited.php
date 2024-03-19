<?php


namespace App\Hooks\User;


use App\Config\SetMailConfig;
use App\Helpers\Traits\BrandInactiveTrait;
use App\Hooks\HookContract;
use App\Models\Core\App\Brand\Brand;

class BeforeUserInvited extends HookContract
{
    use BrandInactiveTrait;

    public function handle()
    {
        $this->actionIfInactive();

        $mailSettings = request()->get('brand_id') ? Brand::find(request()->get('brand_id'))->mailSettings() : [];
        (new SetMailConfig($mailSettings))
            ->clear()
            ->set();
    }

}
