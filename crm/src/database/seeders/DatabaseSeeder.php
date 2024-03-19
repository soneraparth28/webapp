<?php
namespace Database\Seeders;

use Database\Seeders\App\BrandSeeder;
use Database\Seeders\App\NotificationChannelTableSeeder;
use Database\Seeders\App\NotificationEventTableSeeder;
use Database\Seeders\App\NotificationSettingsSeeder;
use Database\Seeders\App\NotificationTemplateSeeder;
use Database\Seeders\App\SettingTableSeeder;
use Database\Seeders\Auth\PermissionRoleTableSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\TypeSeeder;
use Database\Seeders\Auth\UserRoleTableSeeder;
use Database\Seeders\Auth\UserTableSeeder;
use Database\Seeders\Brand\Notification\BrandNotificationEventSeeder;
use Database\Seeders\Brand\Notification\BrandNotificationSettingSeeder;
use Database\Seeders\Builder\CustomFieldTypeSeeder;
use Database\Seeders\Demo\Segment\SegmentLogicRelationTableSeeder;
use Database\Seeders\Demo\Segment\SegmentLogicSeeder;
use Database\Seeders\Privacy\PrivacySettingSeeder;
use Database\Seeders\Status\StatusSeeder;
use Database\Seeders\Template\TemplateTableSeeder;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use TruncateTable, DisableForeignKeys;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        Model::unguard();
        $this->disableForeignKeys();

        $this->call(StatusSeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(UserRoleTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(CustomFieldTypeSeeder::class);
        $this->call(NotificationChannelTableSeeder::class);
        $this->call(NotificationEventTableSeeder::class);
        $this->call(BrandNotificationEventSeeder::class);
        $this->call(NotificationSettingsSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(SegmentLogicSeeder::class);
        $this->call(SegmentLogicRelationTableSeeder::class);
//        $this->call(BrandNotificationSettingSeeder::class);
//        $this->call(TemplateTableSeeder::class);
        $this->call(NotificationTemplateSeeder::class);
        $this->call(PrivacySettingSeeder::class);
        $this->call(BrandGroupSeeder::class);

        $this->enableForeignKeys();
        Model::reguard();
    }
}
