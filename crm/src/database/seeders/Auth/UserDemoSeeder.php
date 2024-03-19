<?php
namespace Database\Seeders\Auth;

use App\Jobs\User\UserInvited;
use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use Illuminate\Database\Seeder;

class UserDemoSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {

        Role::insert([
            [
                'name' => 'Manager',
                'type_id' => 1,
                'created_by' => 1
            ],
            [
                'name' => 'Moderator',
                'type_id' => 1,
                'created_by' => 1
            ],
        ]);
        $permissions = Permission::pluck('id')->toArray();

        Role::where('id', '!=', 1)->get()->each(function (Role $role) use ($permissions) {
            $role->permissions()->attach($permissions);
        });

        $role = Role::where('id', '!=', 1)->get();
        factory(User::class, 10)->create()->each(function (User $user) use ($role){
            $random_role = $role->random();
            $user->assignRole($random_role->id);
            if ($random_role->brand_id) {
                UserInvited::dispatch($user)
                    ->onConnection('sync');
            }
        });

        $brand_admin = User::query()->create([
            'first_name' => 'Tony',
            'last_name' => 'Stark',
            'email' => 'brand_admin@demo.com',
            'password' => '123456',
            'status_id' => resolve(\App\Repositories\App\StatusRepository::class)->userActive()
        ]);
        $brand_role = $role->whereNotNull('brand_id')->first();
        $brand_admin->assignRole($brand_role->id);
        UserInvited::dispatch($brand_admin)
            ->onConnection('sync');

    }
}
