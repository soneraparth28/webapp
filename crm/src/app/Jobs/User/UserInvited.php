<?php

namespace App\Jobs\User;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserInvited implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $this->user->brands()->sync(
            optional($this->user->roles)->filter(function (Role $role) {
                return $role->brand_id;
            })->pluck('brand_id') ?? []
        );
    }
}
