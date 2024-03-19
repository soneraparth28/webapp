<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('settings')" icon="settings"/>

        <app-tab :tabs="tabs"
                 icon="settings"/>
    </div>
</template>

<script>
    export default {
        name: "BrandSettingLayout",
        props: {
            permissions: {}
        },
        data() {
            const permissions = this.permissions
            return {
                tabs: [
                    {
                        name: this.$t('delivery'),
                        title: this.$t('delivery'),
                        component: "app-brand-delivery-settings",
                        props: {alias: 'brand'},
                        permission: permissions.delivery_settings
                    },
                    {
                        name: this.$fieldTitle('custom', 'fields', true),
                        title: this.$t('custom_fields'),
                        component: "app-brand-custom-fields",
                        permission: permissions.custom_field,
                        headerButton: {
                            label: this.$fieldTitle('add', 'custom_field', true),
                            class: 'btn btn-primary'
                        }
                    },
                    {
                        name: this.$t('notification'),
                        title: this.$t('notification'),
                        component: "app-all-notification-settings",
                        props: {alias: 'brand', specific: true},
                        permission: permissions.notification
                    },
                    {
                        name: this.$t('api'),
                        title: this.$t('api'),
                        component: "app-subscriber-api-url",
                        props: {alias: 'brand'},
                        permission: permissions.subscriber_api
                    }
                ]
            }
        }
    }
</script>
