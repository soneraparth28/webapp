import Vue from 'vue'
import {axiosGet, urlGenerator} from "../../../Helpers/AxiosHelper";
import {notification_event_setting, notifications} from '../../../config/apiUrl'
import {calenderTime, onlyTime, optional} from '../../../Helpers/helpers'
import axios from 'axios'

const state = {
    notification_setting: {},
    notification_settings: [],
    notifications: {}
};

const actions = {
    getNotificationSettingWithEvent({commit, state}, payload){
        axiosGet(`${notification_event_setting}${payload}`).then(({data}) => {
            commit('NOTIFICATION_EVENT_SETTING', data);
        })
    },

    getNotifications({commit, state}, nextPage = notifications) {
        const url = nextPage === notifications ? `${urlGenerator(nextPage)}?per_page=4&unread=1` : `${nextPage}&per_page=4&unread=1`;
        commit('SET_LOADER', true);
        axios.get(url).then(({data}) => {
            commit('SET_LOADER', false);
            commit('NOTIFICATIONS', data);
        })
    }

};

const mutations = {
    NOTIFICATION_EVENT_SETTING(state, data) {
        state.notification_setting = data;
    },
    NOTIFICATIONS(state, data){
        if (!Object.keys(state.notifications).length){
            state.notifications = data;
        }else {
            state.notifications.next_page_url = data.next_page_url;
            state.notifications.data = state.notifications.data.concat(data.data);
        }
    }
};

const getters = {
    getFormattedNotifications: state => {
        let notifications = {
            ...state.notifications
        };
        if (state.notifications.data) {
            notifications.data = state.notifications.data.map(notification => {
                return {
                    message: notification.data.message,
                    url: notification.data.url,
                    notifier_name: optional(notification, 'notifier', 'full_name'),
                    profile_picture: optional(notification, 'notifier', 'profile_picture', 'full_url'),
                    notified_at: calenderTime(notification.created_at, true),
                    notified_time: onlyTime(notification.created_at),
                    id: notification.id,
                    status: notification.read_at == null ? Vue.prototype.$t('new') : Vue.prototype.$t('old')
                }
            });
            notifications.total_unread = state.notifications.data.filter(notification => {
                return notification.read_at == null;
            }).length;

            return notifications;
        }
        return [];
    }

};


export default {
    state,
    getters,
    actions,
    mutations
}
