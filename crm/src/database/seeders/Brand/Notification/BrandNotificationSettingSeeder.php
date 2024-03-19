<?php
namespace Database\Seeders\Brand\Notification;

use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class BrandNotificationSettingSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        $this->enableForeignKeys();
    }
}
