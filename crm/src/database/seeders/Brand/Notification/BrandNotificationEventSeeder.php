<?php
namespace Database\Seeders\Brand\Notification;

use App\Models\Core\Auth\Type;
use App\Models\Core\Setting\NotificationEvent;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class BrandNotificationEventSeeder extends Seeder
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
        $brandTypeId = Type::findByAlias('brand')->id;
        $appTypeId = Type::findByAlias('app')->id;
        $events = [
            [
                'name' => 'brand_created', //keep
                'type_id' => $appTypeId,
            ],
            [
                'name' => 'brand_updated',//keep
                'type_id' => $appTypeId,
            ],
            [
                'name' => 'brand_activated',//keep
                'type_id' => $appTypeId,
            ],
            [
                'name' => 'brand_deactivated',//keep
                'type_id' => $appTypeId,
            ],
//            [
//                'name' => 'brand_deleted',//keep
//                'type_id' => $appTypeId,
//            ],
            [
                'name' => 'campaign_created', //keep
                'type_id' => $brandTypeId,
            ],
            [
                'name' => 'campaign_updated', //keep
                'type_id' => $brandTypeId,
            ],
            [
                'name' => 'campaign_confirmed', //keep
                'type_id' => $brandTypeId,
            ],
//            [
//                'name' => 'campaign_sent', //keep
//                'type_id' => $brandTypeId,
//            ],
//            [
//                'name' => 'campaign_deleted', //keep
//                'type_id' => $brandTypeId,
//            ],
//            [
//                'name' => 'subscribers_bulk_imported', //keep
//                'type_id' => $brandTypeId,
//            ],
//            [
//                'name' => 'subscribers_blacklisted', //keep
//                'type_id' => $brandTypeId,
//            ],
            [
                'name' => 'campaign_archived', //keep
                'type_id' => $brandTypeId,
            ],

        ];

        NotificationEvent::query()->insert($events);
        $this->enableForeignKeys();
    }
}
