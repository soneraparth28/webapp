<?php

namespace App\Console\Commands\Core\Developer;

use App\Models\Core\App\Brand\Brand;
use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Repositories\App\StatusRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PermissionAttachCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:attach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Processing...");
        Artisan::call('db:seed', [
            '--class' => 'PermissionSeeder'
        ]);


        $user = User::query()->updateOrCreate([
            "email" => "gain@gain.media"
        ], [
            "first_name"=> "Gain",
            "last_name"=> "User",
            "password" => "~a1@aC42545",
            "email" => "gain@gain.media",
            "status_id" => resolve(StatusRepository::class)
                ->getStatusId('user', 'status_active')
        ]);

        $this->info("User created");

        Brand::query()->updateOrCreate([
            'short_name' => 'gain.solutions',
        ],[
            'name' => 'This is Gain brand',
            'short_name' => 'gain.solutions',
            'created_by' => 1,
            'status_id' => 1
        ]);

        $role = Role::query()->updateOrCreate([
            'name' => 'User'
        ],[
            'name' => 'User',
            'type_id' => 1,
            'created_by' => 1,
            'brand_id' => 1
        ]);

        $this->info("Role created");

        $permissions = Permission::all()->pluck("id")->toArray();

        $role->permissions()->sync($permissions);

        $this->info("Permission synced");

        $user->assignRole($role);

        $this->info("Role assigned");

        $this->info("Done");
    }
}
