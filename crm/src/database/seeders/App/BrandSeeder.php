<?php
namespace Database\Seeders\App;

use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use App\Services\Brand\BrandNotificationSettingService;
use App\Services\Brand\BrandRoleService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $brand = Brand::query()->findOrNew(1);

        if (!$brand->exists()) {
            $brand->fill([
                'name' => 'Default Brand',
                'short_name' => 'default-brand',
                'created_by' => User::query()->first()->id
            ]);

            if (app()->runningInConsole()) {
                $brand->save();
            }else {
                Brand::withoutEvents(function () use ($brand) {
                    $brand->created_by = User::query()->first()->id;
                    $brand->save();
                });
            }


            $brandTypeId = Type::findByAlias('brand')->id;

            $role = resolve(BrandRoleService::class)
                ->create($brand, $brandTypeId);

            resolve(BrandNotificationSettingService::class)
                ->migrate($brand, $brandTypeId, $role);
        }

        $this->enableForeignKeys();
    }
}
