<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Helpers\Traits\BrandInactiveTrait;
use App\Hooks\HookContract;
use App\Models\Core\Auth\User;

class BeforeUserAttachedToRole extends HookContract
{
    use InstanceCreator, BrandInactiveTrait;

    public function handle()
    {
        $this->actionIfInactive();

        User::query()->whereIn('id',request()->get('users',[]))->get()->map( function(User $user){
            $user->roles()->sync([]);
        });

        collect(request()->get('users',[]))->map(function ($user_id) {
            cache()->forget('app-admin-'.$user_id.'-'.request('brand_id',''));
            cache()->forget('brand-admin-'.$user_id.'-'.request('brand_id',''));
        });
    }
}