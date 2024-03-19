<template>
    <div class="content-wrapper">
        <app-page-top-section
            :title="$fieldTitle('app', 'settings', true)"
            :directory="$t('settings')"
            icon="settings"
        />
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
    export default {
        name: "SettingLayout",
        props: {
            settingsPermissions: {
                required: true,
            }
        },
        data() {
            const permissions = JSON.parse(this.settingsPermissions)
            return {
                tabs: [
                    {
                        name: this.$t('general'),
                        title: this.$t('general'),
                        component: "app-general-settings",
                        permission: permissions.general
                    },
                    {
                        name: this.$t('delivery'),
                        title: this.$t('delivery'),
                        component: "app-delivery-settings",
                        props: {alias: 'app'},
                        permission: permissions.delivery
                    },
                    {
                        name: this.$t('cron_job'),
                        title: this.$t('cron_job'),
                        component: "app-cron-job-settings",
                        props: {alias: 'app'},
                    },
                    {
                        name: this.$t('notification'),
                        title: this.$t('notification'),
                        component: "app-all-notification-settings",
                        permission: permissions.notification
                    },
                    {
                        name: this.$t('update'),
                        title: this.$t('update'),
                        component: "app-update",
                        permission: permissions.update
                    },
                ]
            }
        }
    }
</script>
