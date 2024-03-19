import Vue from 'vue'

//Helper for front-end
Vue.component('app-message', require('./Component/Helper/Message/Message').default);
Vue.component('app-submit-button', require('./Component/Helper/Button/SubmitButton').default);
Vue.component('app-cancel-button', require('./Component/Helper/Button/CancelButton').default);
Vue.component('app-back-button', require('./Component/Helper/Button/BackButton').default);
Vue.component('app-default-button', require('./Component/Helper/Button/AddNewButton').default);
Vue.component('app-form-group', require('./Component/Helper/Form/FormGroup').default);
Vue.component('app-form-group-selectable', require('./Component/Helper/Form/FormGroupSelectable').default);


//Login
Vue.component('app-auth-login', require('./Component/Views/Auth/Login').default);
Vue.component('app-user-profile', require('./Component/Views/Auth/Profile').default);
Vue.component('app-password-reset', require('./Component/Views/Auth/Password/PasswordReset').default)
Vue.component('app-reset-password', require('./Component/Views/Auth/Password/ResetPassword').default)
Vue.component('modal', require('./Component/Helper/Modal/ModalComponent').default);

//Layout
Vue.component('app-top-navigation-bar', require('./Component/Views/Layout/TopBar').default);
Vue.component('app-setting-layout', require('./Component/Views/Settings/Global/App/SettingLayout').default);
Vue.component('app-brand-setting-layout', require('./Component/Views/Settings/Global/Brand/AppBrandSettingLayout').default);
Vue.component('app-navbar-notification-dropdown', require('./Component/Views/Layout/Components/NotificationsDropdown').default);
Vue.component('app-page-top-section', require('./Component/Helper/PageTopSection/PageTopSection').default);
Vue.component('app-dashboard', require('./Component/Views/Dashboard/Dashboard').default);
Vue.component('app-brand-setting', require('./Component/Views/Settings/BrandSetting/BrandSettingLayout').default);
Vue.component('app-notifications', require('./Component/Views/Auth/Notifications').default);

Vue.component('app-general-settings', require('./Component/Views/Settings/Global/App/Component/GeneralSetting').default)
Vue.component('app-delivery-settings', require('./Component/Views/Settings/Common/DeliverySettings').default)
Vue.component('app-smtp', require('./Component/Views/Settings/Common/Delivery/SMTP').default)
Vue.component('app-ses', require('./Component/Views/Settings/Common/Delivery/AmazonSES').default)
Vue.component('app-mailgun', require('./Component/Views/Settings/Common/Delivery/Mailgun').default)
Vue.component('app-delivery-list', require('./Component/Views/Settings/Common/MailServerLIst').default)
Vue.component('app-notification-template-settings', require('./Component/Views/Settings/Common/NotificationTemplateSettings').default)
Vue.component('app-store-update-notification-templates', require('./Component/Views/Settings/Common/http/StoreUpdateNotificationTemplate').default)
Vue.component('app-all-notification-settings', require('./Component/Views/Settings/Common/NotificationSettings').default)
Vue.component('app-notification-settings', require('./Component/Views/Settings/Common/http/StoreUpdateNotificationSettings').default)
Vue.component('app-brand-privacy', require('./Component/Views/Settings/Global/Brand/Privacy/BrandPrivacy').default)
Vue.component('app-custom-fields', require('./Component/Views/Settings/Global/Brand/CustomField/CustomFields').default)
Vue.component('app-store-update-custom-fields', require('./Component/Views/Settings/Global/Brand/CustomField/StoreUpdateCustomFields').default)
Vue.component('app-mail-template', require('./Component/Views/Settings/Common/http/NotificationTemplate/MailTemplate').default)
Vue.component('app-database-template', require('./Component/Views/Settings/Common/http/NotificationTemplate/DatabaseTemplate').default)
// Vue.component('app-update', require('./Component/Views/Settings/Global/App/Component/Update').default)
// Vue.component('app-update-confirmation-modal', require('./Component/Views/Settings/Global/App/Component/UpdateConfirmationModal').default)
Vue.component('app-test-mail-modal', require('./Component/Views/Settings/Common/Delivery/TestMailModal').default)
Vue.component('app-cron-job-settings', require('./Component/Views/Settings/Common/CronJobSettings').default)

//Brand Settings
Vue.component('app-brand-delivery-settings', require('./Component/Views/Settings/BrandSetting/Delivery/BrandDeliverySettings').default)
Vue.component('app-brand-custom-fields', require('./Component/Views/Settings/BrandSetting/CustomFields/CustomFields').default)
Vue.component('app-subscriber-api-url', require('./Component/Views/Settings/BrandSetting/SubscriberApi').default);

//Feature
Vue.component('app-brands', require('./Component/Views/Brand/Brands').default);
Vue.component('app-brand-group', require('./Component/Views/BrandGroup/BrandGroup').default)
Vue.component('app-brand-group-modal', require('./Component/Views/BrandGroup/BrandGroupModal').default)
Vue.component('app-brand-modal', require('./Component/Views/Brand/BrandModal').default);
Vue.component('app-template-settings', require('./Component/Views/Settings/Global/Brand/Template/Templates').default);

//User Role
Vue.component('app-users-roles', require('./Component/Views/UserRoles/Index').default);
Vue.component('app-roles-modal', require('./Component/Views/UserRoles/UI/RolesModal').default);
Vue.component('app-user-modal', require('./Component/Views/UserRoles/UI/UserModal').default);
Vue.component('image-group', require('./Component/Views/UserRoles/UI/ImageGroup').default);
Vue.component('app-user-invite-modal', require('./Component/Views/UserRoles/UI/UserInviteModal').default);
Vue.component('app-user-invite-confirm', require('./Component/Views/Auth/UserInvitationConfirm').default)


//Brands feature

Vue.component('app-campaigns', require('./Component/Views/Campaign/Campaigns').default);
Vue.component('app-campaigns-show', require('./Component/Views/Campaign/Show').default);
Vue.component('app-campaign-create-edit', require('./Component/Views/Campaign/CampaignCreateEdit').default);
Vue.component('app-campaign-details', require('./Component/Views/Campaign/Components/CampaignDetails').default);
Vue.component('app-campaign-delivery', require('./Component/Views/Campaign/Components/CampaignDelivery').default);
Vue.component('app-campaign-audience', require('./Component/Views/Campaign/Components/CampaignAudience').default);
Vue.component('app-campaign-content', require('./Component/Views/Campaign/Components/CampaignContent').default);
Vue.component('app-campaign-confirmation', require('./Component/Views/Campaign/Components/CampaignConfirmation').default);



Vue.component('app-emails', require('./Component/Views/Email/Emails').default);
Vue.component('app-lists-create-edit', require('./Component/Views/List/ListCreateEdit').default);
Vue.component('app-lists-show', require('./Component/Views/List/Show').default);
Vue.component('app-list', require('./Component/Views/List/List').default);
Vue.component('app-segments', require('./Component/Views/Segment/Segments').default);
Vue.component('app-store-update-segment', require('./Component/Views/Segment/StoreUpdateSegment').default);

Vue.component('app-subscribers-wrapper', require('./Component/Views/Subscriber/SubscriberWrapper').default);
Vue.component('app-subscribers', require('./Component/Views/Subscriber/Subscribers').default);
Vue.component('app-subscriber-create-edit', require('./Component/Views/Subscriber/SubscriberCreateEdit').default);
Vue.component('app-import-preview-modal', require('./Component/Views/Subscriber/Components/ImportPreviewModal').default);
Vue.component('app-blacklisted-subscribers', require('./Component/Views/Subscriber/BlackListedSubscribers').default);
Vue.component('app-subscriber-import', require('./Component/Views/Subscriber/Import').default);
Vue.component('app-unsubscribe', require('./Component/Views/Subscriber/UnSubscribe').default);

Vue.component('app-brand-templates', require('./Component/Views/Template/Templates').default);
Vue.component('app-template-create-edit', require('./Component/Views/Template/TemplateCreateEdit').default);
Vue.component('app-template-card-view', require('./Component/Views/Template/TemplateCard').default);
Vue.component('app-template-preview-modal', require('./Component/Views/Template/UI/TemplatePreviewModal').default);
Vue.component('app-template-choose-modal', require('./Component/Views/Template/UI/ChooseTemplatesModal').default);

Vue.component('app-templates', require('./Component/Views/Template/Templates').default);

// User Profile

Vue.component('app-profile-personal-info', require('./Component/Views/Profile/ProfilePersonalInfo').default);
Vue.component('app-password-change', require('./Component/Views/Profile/PasswordChange').default);
Vue.component('app-activity', require('./Component/Views/Profile/Activity').default);
Vue.component('app-user-media', require('./Component/DataTable/Components/User/UserMedia').default)

//Install
Vue.component('app-server-requirements', require('./Component/Views/Install/Server/Requirements').default);
Vue.component('app-server-permissions', require('./Component/Views/Install/Server/Permissions').default);
Vue.component('app-install-wizard', require('./Component/Views/Install/Layout').default);
Vue.component('app-environment-wizard', require('./Component/Views/Install/Env/Environment').default);

//Update
Vue.component('app-update', require('./Component/Views/Update/template/Update').default);
Vue.component('app-manual-updater', require('./Component/Views/Update/template/ManualUpdater').default);
Vue.component('app-update-confirmation-modal', require('./Component/Views/Update/template/UpdateConfirmationModal').default);

