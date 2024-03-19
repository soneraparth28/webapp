<template>
    <li class="nav-item dropdown keep-inside-clicks-open">
        <a href="#"
           id="notificationDropdown"
           class="d-flex align-items-center nav-link count-indicator dropdown-toggle"
           @click.prevent="randomDataGenerate"
           data-toggle="dropdown">
            <app-icon :name="'bell'"/>
            <span :class="`${ !(notifications.data && notifications.data.length && notifications.total_unread) || 'count-symbol bg-primary spinner-grow' }`"/>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown notification-dropdown custom-scrollbar"
             aria-labelledby="notificationDropdown"
             v-if="notifications.data && notifications.data.length">
            <h6 class="p-primary mb-0 primary-text-color">
                <a :href="brand ? urlGenerator(`/brands/${brand.short_name}/notifications/list`) : urlGenerator(`/admin/notifications/list`)">{{ $fieldTitle('all_notifications') }}</a>
                <span class="badge badge-primary badge-sm badge-circle float-right">
                    {{ notifications.total_unread }}
                </span>
            </h6>
            <div class="dropdown-divider" v-if="notifications.data && notifications.data.length"/>
            <div class="dropdown-items-wrapper custom-scrollbar">
                <a class="dropdown-item"
                   v-for="notification in notifications.data"
                   href="#"
                   @click="readNotification(notification)"
                   :key="notification.id" >

                    <div class="media">
                        <div class="avatars-w-50 mr-3">
                            <app-avatar :title="notification.notifier_name"
                                        status=""
                                        :shadow="false"
                                        :img="notification.profile_picture"
                                        :alt-text="notification.notifier_name"/>
                        </div>

                        <div class="media-body">
                            <p class="my-0 media-heading" v-html="purify(notification.message)"></p>
                            <span class="primary-text-color link">
                                <span class="mr-3">{{ notification.notified_at }}</span>
                                <span>{{ notification.notified_time }}</span>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="row" v-if="notifications.next_page_url">
                <div class="col-12 text-center">
                    <a class="btn btn-sm btn-clear" href="#" @click="getNotifications(notifications.next_page_url)">
                        <loader v-if="loading"></loader>
                        <i class="fas fa-arrow-alt-circle-down"></i>
                    </a>
                </div>
            </div>
        </div>
        <div v-else
             class="dropdown-menu dropdown-menu-right navbar-dropdown notification-dropdown no-notification-dropdown p-primary"
             aria-labelledby="notificationDropdown">
            <div class="d-flex justify-content-center">
                <img :src="noNotificationImg" class="no-notification-img"
                     alt="">
            </div>
            <p class="text-center font-size-80 m-0 pt-2 pb-0">{{noNotificationTitle}}</p>
        </div>
    </li>
</template>

<script>
    import { textTruncate } from '../../../../Helpers/helpers'
    import { mapActions, mapState } from 'vuex'
    import Loader from "../../../Helper/Loader/Loader";
    import { axiosPost } from '../../../../Helpers/AxiosHelper'
    import { urlGenerator } from "../../../../Helpers/AxiosHelper";
    import {purify} from "../../../../../core/helpers/purifier/HTMLPurifyHelper";

    export default {
        name: "NotificationDropdown",
        components: {Loader},
        data() {
            return {
                purify,
                all_notifications: [],
                urlGenerator,
                noNotificationImg: '',
                noNotificationTitle: '',
                noNotificationData: [
                    {
                        img: '/images/no-notifications/Cheers',
                        title: this.$t('no_notification_one')
                    },
                    {
                        img: '/images/no-notifications/IceCream',
                        title: this.$t('no_notification_two')
                    },
                    {
                        img: '/images/no-notifications/Music',
                        title: this.$t('no_notification_three')
                    }
                ],
            }
        },
        computed: {
            ...mapState({
                loading: state => state.loading,
                brand: state => state.brand
            }),
            notifications() {
                return this.$store.getters.getFormattedNotifications;
            }
        },
        methods: {
            ...mapActions([
                'getNotifications'
            ]),
            textTruncate(str, length, ending) {
                return textTruncate(str, length, ending);
            },
            readNotification(notification) {
                axiosPost(`admin/user/notifications/mark-as-read/${notification.id}`).then(({data}) => {
                    if (data.data.url) {
                        window.location = data.data.url;
                    }
                    this.getNotifications();
                });
            },
            randomDataGenerate() {
                if(this.notifications.data.length===0){
                    let index = Math.floor(Math.random() * this.noNotificationData.length);
                    if (this.$store.state.theme.darkMode) {
                        this.noNotificationImg = urlGenerator(this.noNotificationData[index].img + '-Dark.png');
                    } else {
                        this.noNotificationImg = urlGenerator(this.noNotificationData[index].img + '-Light.png');
                    }
                    this.noNotificationTitle = this.noNotificationData[index].title;
                }
            }
        },
        created() {
            this.getNotifications();
        }
    }
</script>
