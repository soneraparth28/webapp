<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Type;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class PermissionChildAppTableSeeder extends Seeder
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
        $brand_type_id = Type::findByAlias('brand')->id;
        $app_type_id = Type::findByAlias('app')->id;

        $permissions = [
            //brands
            [
                'name' => 'view_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'create_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'update_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'delete_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'users_lists_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'users'
            ],
            /*[
                'name' => 'user_list_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],*/
            /*[
                'name' => 'attach_users_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],*/
           /* [
                'name' => 'detach_users_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'brand_list_users',
                'type_id' => $brand_type_id,
                'group_name' => 'brands'
            ],*/
            [
                'name' => 'update_settings_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'manage_brand_dashboard',
                'type_id' => $brand_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'create_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'view_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'update_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
            [
                'name' => 'delete_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands'
            ],
//            [
//                'name' => 'attach_brand_brand_groups',
//                'type_id' => $brand_type_id,
//                'group_name' => 'brands'
//            ],
//            [
//                'name' => 'detach_brand_brand_groups',
//                'type_id' => $brand_type_id,
//                'group_name' => 'brands'
//            ],
            /*[
                'name' => 'view_brands_brand_groups',
                'type_id' => $brand_type_id,
                'group_name' => 'brands'
            ],*/
            //end of brands
            // Segments
            [
                'name' => 'view_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],
            [
                'name' => 'create_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],
            [
                'name' => 'update_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],
            [
                'name' => 'delete_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],
            [
                'name' => 'view_brand_segment_counts',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],
           /* [
                'name' => 'duplicate_segment_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
            ],*/

            //Black List Subscriber
            [
                'name' => 'add_to_blacklist_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'remove_from_blacklist_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'view_subscribers_black_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],

            //Context menu actions
            [
                'name' => 'add_to_lists_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'remove_from_lists_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'bulk_destroy_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'update_status_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],

            //Subscribers
            [
                'name' => 'view_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'create_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'update_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'delete_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'import_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],


            //Subscriber Bulk Import
            [
                'name' => 'view_imported_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'bulk_import_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],
            [
                'name' => 'quick_import_subscribers',
                'type_id' => $brand_type_id,
                'group_name' => 'subscriber'
            ],

            // Lists
            [
                'name' => 'view_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'lists'
            ],
            [
                'name' => 'create_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'lists'
            ],
            [
                'name' => 'update_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'lists'
            ],
            [
                'name' => 'delete_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'lists'
            ],
            [
                'name' => 'dynamic_subscribers_lists',
                'type_id' => $brand_type_id,
                'group_name' => 'lists'
            ],

            // Campaigns
            [
                'name' => 'view_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'create_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'update_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'delete_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'delivery_settings_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'audiences_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'template_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'confirm_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'email_logs_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'subscribers_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'test_campaign',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'duplicate_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
            ],
            [
                'name' => 'campaigns_count_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'campaigns_count_app',
                'type_id' => $brand_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'email_statistics_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'email_statistics_app',
                'type_id' => $app_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'subscribers_count_brands',
                'type_id' => $brand_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'subscribers_count_app',
                'type_id' => $app_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'update_delivery_settings',
                'type_id' => $brand_type_id,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_delivery_settings',
                'type_id' => $brand_type_id,
                'group_name' => 'settings'
            ],
            [
                'name' => 'view_notification_events',
                'type_id' => $brand_type_id, //Do something
                'group_name' => 'notification_events'
            ],
            [
                'name' => 'view_sns_subscription',
                'type_id' => $brand_type_id,
                'group_name' => 'amazon_ses'
            ],
            [
                'name' => 'confirm_sns_subscription',
                'type_id' => $brand_type_id,
                'group_name' => 'amazon_ses'
            ],
            [
                'name' => 'view_emails',
                'type_id' => $brand_type_id,
                'group_name' => 'amazon_ses'
            ],
            [
                'name' => 'check_for_updates',
                'type_id' => $brand_type_id,
                'group_name' => 'update'
            ],
            [
                'name' => 'update_app',
                'type_id' => $brand_type_id,
                'group_name' => 'update'
            ],

        ];

        $this->enableForeignKeys();
        Permission::query()->insert($permissions);
    }
}
