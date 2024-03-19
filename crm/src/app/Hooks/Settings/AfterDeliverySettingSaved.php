<?php


namespace App\Hooks\Settings;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use Illuminate\Support\Facades\Artisan;

class AfterDeliverySettingSaved extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        Artisan::call('queue:restart');
    }
}