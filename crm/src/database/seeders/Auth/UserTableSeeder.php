<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\User;
use App\Repositories\App\StatusRepository;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        $user = User::query()->findOrNew(1);

        if (!$user->exists()) {
            $user->fill([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'admin@demo.com',
                'password' => Hash::make('123456'),
                'status_id' => resolve(StatusRepository::class)->userActive()
            ]);

            $user->save();
        }

        $this->enableForeignKeys();
    }
}
