<?php

namespace App\Jobs;

use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Type;
use App\Services\Brand\BrandNotificationSettingService;
use App\Services\Brand\BrandRoleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BrandCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**@var Brand $brand*/
    protected $brand;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
        $this->user = auth()->user();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $brandTypeId = Type::findByAlias('brand')->id;

        $role = resolve(BrandRoleService::class)
            ->create($this->brand, $brandTypeId);

        resolve(BrandNotificationSettingService::class)
            ->migrate($this->brand, $brandTypeId, $role);

    }
}
