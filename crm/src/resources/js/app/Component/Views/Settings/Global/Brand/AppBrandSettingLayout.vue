<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$fieldTitle('brand', 'settings', true)" :directory="$t('settings')" icon="settings" />
        <app-tab
            :tabs="tabs"
            icon="settings"
        />
    </div>
</template>

<script>
    import {custom_fields} from "../../../../../config/apiUrl";

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
                        name: this.$t('delivery'),
                        title: this.$t('delivery'),
                        component: "app-delivery-settings",
                        props: {alias: 'brand'},
                        permission: permissions.delivery
                    },
                    {
                        name: this.$t('tracking'),
                        title: this.$t('tracking'),
                        component: "app-brand-privacy",
                        props: "",
                        permission: permissions.privacy
                    },
                    {
                        name: this.$fieldTitle('custom', 'fields', true),
                        title: this.$t('custom_fields'),
                        component: "app-custom-fields",
                        permission: permissions.custom_fields,
                        headerButton: {
                            label: this.$fieldTitle('add', 'custom_field', true),
                            class: 'btn btn-primary',
                        }
                    },
                    {
                        name: this.$t('notification'),
                        title: this.$t('notification'),
                        component: "app-all-notification-settings",
                        props: {alias: 'brand'},
                        permission: permissions.notification
                    },
                ]
            }
        }
    }
</script>
