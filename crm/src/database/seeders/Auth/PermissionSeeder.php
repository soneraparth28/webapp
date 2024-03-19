<?php
namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Type;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
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
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'create_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'update_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'delete_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            /*[
                'name' => 'user_list_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],*/
            /*[
                'name' => 'attach_users_brands',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],*/
            /* [
                 'name' => 'detach_users_brands',
                 'type_id' => $brand_type_id,
                 'group_name' => 'brands_and_groups'
             ],
             [
                 'name' => 'brand_list_users',
                 'type_id' => $brand_type_id,
                 'group_name' => 'brands_and_groups'
             ],*/
            [
                'name' => 'create_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'view_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'update_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'delete_brand_groups',
                'type_id' => $app_type_id,
                'group_name' => 'brands_and_groups'
            ],
            [
                'name' => 'invite_user',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'view_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'update_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'delete_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'attach_roles_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            [
                'name' => 'detach_roles_users',
                'type_id' => null,
                'group_name' => 'users'
            ],
            /*[
                'name' => 'change_password_users',
                'type_id' => null,
                'group_name' => 'users'
            ],*/
           /* [
                'name' => 'change_profile_picture_users',
                'type_id' => null,
                'group_name' => 'users'
            ],*/
            //roles
            [
                'name' => 'view_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'create_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'update_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'delete_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'attach_users_to_roles',
                'type_id' => null,
                'group_name' => 'roles'
            ],
            [
                'name' => 'view_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],
            [
                'name' => 'update_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],
            [
                'name' => 'view_delivery_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],
            [
                'name' => 'update_delivery_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],
            /*[
                'name' => 'view_notification_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],
            [
                'name' => 'update_notification_settings',
                'type_id' => $app_type_id,
                'group_name' => 'app_settings'
            ],*/
            [
                'name' => 'view_brand_delivery_settings',
                'type_id' => $app_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'update_brand_delivery_settings',
                'type_id' => $app_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'update_brand_privacy_settings',
                'type_id' => $app_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'view_brand_privacy_settings',
                'type_id' => $app_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'update_specific_brand_delivery_settings',
                'type_id' => $brand_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'view_specific_brand_delivery_settings',
                'type_id' => $brand_type_id,
                'group_name' => 'brand_settings'
            ],
            [
                'name' => 'view_notification_settings',
                'type_id' => null,
                'group_name' => 'notifications'
            ],
            [
                'name' => 'update_notification_settings',
                'type_id' => null,
                'group_name' => 'notifications'
            ],
            [
                'name' => 'view_notification_templates',
                'type_id' => $app_type_id,
                'group_name' => 'notifications'
            ],
            [
                'name' => 'update_notification_templates',
                'type_id' => $app_type_id,
                'group_name' => 'notifications'
            ],
            [
                'name' => 'view_custom_fields',
                'type_id' => null,
                'group_name' => 'custom_fields'
            ],
            [
                'name' => 'create_custom_fields',
                'type_id' => null,
                'group_name' => 'custom_fields'
            ],
            [
                'name' => 'update_custom_fields',
                'type_id' => null,
                'group_name' => 'custom_fields'
            ],
            [
                'name' => 'delete_custom_fields',
                'type_id' => null,
                'group_name' => 'custom_fields'
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
                'group_name' => 'emails'
            ],
            [
                'name' => 'delete_emails',
                'type_id' => $brand_type_id,
                'group_name' => 'emails'
            ],
            //brand will be here
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
                'name' => 'pause_or_resume_campaigns',
                'type_id' => $brand_type_id,
                'group_name' => 'campaigns'
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
            //segments
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
             [
                'name' => 'copy_segments',
                'type_id' => $brand_type_id,
                'group_name' => 'segments'
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
            [
                'name' => 'generate_subscriber_api_url',
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
            //Templates
            [
                'name' => 'view_templates',
                'type_id' => null,
                'group_name' => 'templates'
            ],
            [
                'name' => 'create_templates',
                'type_id' => null,
                'group_name' => 'templates'
            ],
            [
                'name' => 'update_templates',
                'type_id' => null,
                'group_name' => 'templates'
            ],
            [
                'name' => 'delete_templates',
                'type_id' => null,
                'group_name' => 'templates'
            ],
            [
                'name' => 'manage_dashboard',
                'type_id' => $app_type_id,
                'group_name' => 'dashboard'
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
                'name' => 'manage_brand_dashboard',
                'type_id' => $brand_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'subscribers_count_app',
                'type_id' => $app_type_id,
                'group_name' => 'dashboard'
            ],
            [
                'name' => 'check_for_updates',
                'type_id' => $app_type_id,
                'group_name' => 'update'
            ],
            [
                'name' => 'update_app',
                'type_id' => $app_type_id,
                'group_name' => 'update'
            ],
        ];
        $this->enableForeignKeys();
        Permission::query()->insert($permissions);
    }
}
