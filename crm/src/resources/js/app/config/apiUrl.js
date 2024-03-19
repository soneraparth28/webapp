export const languages = 'admin/languages';
export const config = 'admin/settings-format';
export const notification_channels = 'admin/app/notification-channels';
export const notification_events = 'admin/app/notification-events';
export const app_general_settings = 'admin/app/settings';
export const mail_settings_list = 'admin/app/settings/delivery-settings';
export const brand_default_mail_settings = 'admin/brand/settings/delivery';
export const app_delivery_quotas = 'admin/app/delivery-quotas';
export const notification_setting = 'admin/app/notification-settings/';
export const notification_event_setting = 'admin/notification-events/settings/';
export const notifications = 'admin/user/notifications';
export const generateShortNames = 'admin/brands/generate-short-names';

export const users = 'admin/users';
export const roles = 'admin/roles';
export const brand_app_roles = 'admin/all-roles';
export const detach_roles = 'admin/auth/users/detach-roles'
export const app_templates = 'admin/app/templates';
export const custom_fields = 'admin/app/custom-fields';
export const custom_field_types = 'admin/app/custom-field-types';
export const brand_privacy = 'admin/app/settings/brand-privacy';
export const statuses = 'admin/statuses';
export const subscriber_count = 'admin/app/subscribers-count';
export const campaign_count = 'admin/app/campaigns-count';
export const brand_count = 'admin/brands-count';
export const email_statics = 'admin/app/email-statistics';
export const logic_names = 'admin/logic-names';
export const logic_operators = 'admin/logic-operators';
export const app_updates = 'app/updates';
export const permissions = 'admin/auth/permissions';
export const sns_subscription = 'admin/sns-subscriptions';
export const test_mail = 'admin/app/test-mail/send';
export const check_mail_settings = 'admin/app/check-mail-settings';
export const mail_settings_view = '/admin/settings?tab=Delivery';
export const cron_job_settings = '/admin/app/settings/cronjob';
//Update
export const APP_UPDATE = `app/updates`;
export const APP_UPDATE_INSTALL = `app/updates/install`;
export const CLEAR_CACHE= `app/clear-cache`;
export const GET_UPDATE_URL = `app/generated-update-url-purchase-code`;

//Brand Urls
const brandPath = `brands/${window.hasOwnProperty('brand') ? window.brand.short_name : ''}`;

export const brand_users = `${brandPath}/users`;
export const brand_user_invite = `${brandPath}/users/invite-user`;
export const brand_user_cancel_invitation = `${brandPath}/users/cancel-invitation`;
export const brand_roles = `${brandPath}/roles`;
export const brand_roles_select = `${brandPath}/roles/select`;
export const brand_attach_roles = `${brand_users}/attach-roles`;
export const brand_detach_roles = `${brand_users}/detach-roles`;
export const campaigns = `${brandPath}/campaigns`;
export const campaigns_create = `/${brandPath}/campaigns/create`;
export const subscribers = `/${brandPath}/subscribers`;
export const selectable_subscribers = `/${brandPath}/subscribers/select`;
export const subscribers_add_to_lists = `${subscribers}/add-to-lists`;
export const subscribers_remove_from_lists = `${subscribers}/remove-from-lists`;
export const subscribers_add_to_blacklist = `${subscribers}/add-to-blacklist`;
export const subscribers_bulk_destroy = `${subscribers}/bulk-destroy`;
export const subscribers_change_status = `${subscribers}/update-status`;
export const selectable_campaigns = `/${brandPath}/campaigns/select`;
export const selectable_list = `/${brandPath}/lists/select`;
export const subscriber_create = `/${brandPath}/subscribers/create`
export const subscriber_store = `${brandPath}/subscribers`
export const subscriber_view_imported = `${brandPath}/subscribers/view-imported`
export const subscriber_bulk_import = `/${brandPath}/subscribers/bulk-import`
export const subscriber_import = `/${brandPath}/subscribers/import`
export const list_create = `${brandPath}/lists/create`
export const lists = `${brandPath}/lists`;
export const lists_front_end = `${brandPath}/list/page`;
export const list_view = `${brandPath}/list`;
export const brand_templates = `${brandPath}/templates`;
export const brand_delivery_settings = `${brandPath}/settings/delivery`;
export const brand_delivery_settings_delete = `${brandPath}/settings/delivery/delete`;
export const brand_delivery_quotas = `${brandPath}/delivery-quotas`;
export const brand_delivery_provider = `${brandPath}/delivery/provider`;
export const brand_permissions = `${brandPath}/permissions`
export const brand_test_mail = `${brandPath}/test-mail/send`;
export const check_brand_mail_settings = `${brandPath}/check-mail-settings`;
export const brand_mail_settings_view = `${brandPath}/settings/page?tab=Delivery`;

export const subscribers_front_end = `${brandPath}/subscriber/list`;
export const campaigns_front_end = `${brandPath}/campaign/list`;
export const brand_subscriber_count = `${brandPath}/subscribers-count`;
export const brand_campaign_count = `${brandPath}/campaigns-count`;
export const brand_email_statics = `${brandPath}/email-statistics`;
export const brand_segment_count = `${brandPath}/segments-count`;

export const segments = `${brandPath}/segments`;
export const segments_front_end = `${brandPath}/segment`;
export const custom_field = `${brandPath}/custom-field`;

export const template_create = `/${brandPath}/templates/create`;
export const templates = `${brandPath}/templates`;
export const templates_card_view = `${brandPath}/template-card-view/list`;
export const templates_list_view = `${brandPath}/template-list-view/list`;

// SelectBox urls
export const select_segments_api = `${brandPath}/segments/select`;
export const select_subscribers_api = `${brandPath}/subscribers/select`;
export const brand_selectable_users = `${brandPath}/selectable-users`
export const brand_selectable_roles = `${brandPath}/selectable-roles`


export const email_logs = `${brandPath}/email-logs`;
export const email_log = `${brandPath}/email-log`;
export const user_brands = `admin/user/brands`;

export const brand_custom_field = `${brandPath}/settings/custom-fields`;
export const brand_sns_subscriptions = `${brandPath}/sns-subscriptions`;
export const brand_notification_settings = `${brandPath}/notification-settings`;
export const subscriber_api_url = `${brandPath}/get-subscriber-api-url`;
