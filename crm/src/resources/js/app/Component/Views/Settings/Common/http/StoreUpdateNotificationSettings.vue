<template>
    <modal id="notification-settings"
           v-model="showModal"
           :title="`${$t('settings')}: ${Object.keys(notification_event).length ?  $optional(notification_event, 'translated_name') : ''}`"
           @submit="submitData"
           :scrollable="false"
           :loading="loading"
           :preloader="loader">
        <form method="post" ref="form" :data-url="`admin/app/notification-settings/${notification_settings.id}`">
            <app-form-group
                v-show="false"
                type="select"
                :label="$fieldTitle('notification', 'event')"
                v-model="notification_settings.notification_event_id"
                :list="notification_events"
                list-value-field="translated_name"
                :error-message="$errorMessage(errors, 'context')"
            />

            <app-form-group
                v-show="true"
                type="multi-select"
                :label="$t('choose', {field: $fieldTitle('notification', 'channel')})"
                v-model="notification_settings.notify_by"
                :list="getNotificationChannels()"
                list-value-field="name"
                :isAnimatedDropdown="true"
                :error-message="$errorMessage(errors, 'notify_by')"
            />

            <fieldset>
                <legend class="font-size-default pt-0 mb-3">
                    {{ $fieldTitle('notification', 'audiences') }}
                </legend>

                <app-form-group
                    type="multi-select"
                    :label="$t('roles')"
                    v-model="notification_settings.roles"
                    :list="getFormattedRoles"
                    :isAnimatedDropdown="true"
                    :error-message="$errorMessage(errors, 'audiences.0.audience_type')"
                />

                <app-form-group
                    type="multi-select"
                    :label="$t('users')"
                    v-model="notification_settings.users"
                    :list="getFormattedUsers"
                    :isAnimatedDropdown="true"
                    :error-message="$errorMessage(errors, 'audiences.1.audience_type')"
                />

            </fieldset>
        </form>
    </modal>
</template>

<script>
    import {mapState, mapGetters} from 'vuex';
    import FormHelperMixins from "../../../../../Mixins/Global/FormHelperMixins";
    import {users, roles} from '../../../../../Helpers/NotificationSettings';
    import {axiosGet, axiosPatch} from "../../../../../Helpers/AxiosHelper";
    import {brand_notification_settings, notification_setting} from '../../../../../config/apiUrl';
    import ModalMixin from "../../../../../Mixins/Global/ModalMixin";
    import NotificationEventMixin from "../Mixin/NotificationEventMixin";

    export default {
        name: "StoreUpdateNotificationSettings",
        mixins: [FormHelperMixins, ModalMixin, NotificationEventMixin],
        props: {
            alias: {
                required: true,
                type: String,
                default: 'app'
            },
            eventId: {
                required: true
            },
            specificBrand: {
                default: false
            }
        },
        data() {
            return {
                notification_settings: {
                    notification_event_id: '',
                    notify_by: [],
                    roles: [],
                    users: [],
                    id: ''
                },
                notification_event: {},
            }
        },
        methods: {
            submitData() {
                this.loading = true;
                const notification_settings = {
                    ...this.notification_settings,
                    audiences: [
                        this.notification_settings.roles.length ? {
                            audience_type: 'roles',
                            audiences: this.notification_settings.roles
                        } : '',
                        this.notification_settings.users.length ? {
                            audience_type: 'users',
                            audiences: this.notification_settings.users
                        } : '',
                    ]
                };

                notification_settings.audiences = notification_settings.audiences.filter(a => a);

                const url = this.specificBrand ? `${brand_notification_settings}/${this.notification_event.id}` : `${notification_setting}${this.notification_settings.id}`;

                axiosPatch(url, notification_settings).then(({data}) => {
                    this.toastAndReload(data.message, 'notification-event')
                    $('#notification-settings').modal('hide')
                }).catch(({response}) => {
                    this.errors = response.data.errors || {};
                    if (response.status != 422)
                        this.$toastr.e(response.data.message || response.statusText)
                    this.message = '';
                }).finally(res => {
                    this.loading = false;
                })
            },

            getNotificationEvent() {
                axiosGet(`${brand_notification_settings}/${this.eventId}`).then(response => {
                    this.notification_event = response.data;
                })
            }

        },
        computed: {
            ...mapState({
                notification_channels: state => state.additional.notification_channels,
                notification_events: state => state.additional.notification_events,
                loader: state => state.loading
            }),
            ...mapGetters([
                'getFormattedUsers',
                'getFormattedRoles'
            ])
        },
        created() {
            this.$store.dispatch('getUsers', {alias: this.specificBrand ? 'brand' : 'app'});
            this.$store.dispatch('getRoles', {alias: this.specificBrand ? 'brand' : 'app'});
            if (!this.specificBrand) {
                this.$store.dispatch('getNotificationEvents', this.alias)
            } else {
                this.$store.dispatch('getNotificationChannels');
                this.getNotificationEvent();
            }
        },
        watch: {
            'notification_settings.notification_event_id': {
                handler: function (notification_event_id) {
                    if (this.notification_events.length && !this.specificBrand) {
                        this.notification_event = this.collection(this.notification_events)
                            .find(notification_event_id);
                    }
                }
            },
            notification_events: {
                handler: function (notification_events) {
                    if (notification_events.length) {
                        this.notification_event = this.collection(notification_events)
                            .find(this.notification_settings.notification_event_id);
                    }
                },
                deep: true
            },
            notification_event: {
                handler: function (notification_event) {
                    if (Object.keys(notification_event).length) {
                        this.notification_settings.roles = roles(notification_event);
                        this.notification_settings.users = users(notification_event);
                        this.notification_settings.notify_by = this.$optional(notification_event.settings, 'notify_by') || [];
                        this.notification_settings.id = this.$optional(notification_event.settings, 'id');
                    }
                },
                deep: true
            },
            eventId: {
                handler: function (eventId) {
                    this.notification_settings.notification_event_id = eventId;
                },
                immediate: true
            },
        }
    }
</script>

<style scoped>

</style>
