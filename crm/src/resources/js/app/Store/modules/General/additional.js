import { axiosGet } from '../../../Helpers/AxiosHelper'
import {
    languages,
    config,
    notification_channels,
    notification_events,
    statuses,
    brand_delivery_provider
} from '../../../config/apiUrl';
import {configFormatter} from '../../../Helpers/helpers'

const state = {
    languages: [],
    configs: {},
    notification_channels: [],
    notification_events: [],
    statuses: [],
    provider: ''
};

const getters = {
    getFormattedConfig: state => payload =>  {
        if (Object.keys(state.configs).length) {
            return state.configs[payload].map(format => configFormatter(format))
        }
        return [];
    },

    getNotificationEvent: state => payload => {
        if (state.notification_events.length) {
            return state.notification_events.find(nEvent => nEvent.id === Number(payload))
        }
        return {};
    },

    getSpecificContextConfig: state => payload => {
        return state.configs[payload];
    }

};

const actions = {
    getLanguages({commit, state}) {
        if (!state.languages.length) {
            axiosGet(languages).then(({data}) => {
                commit('LANGUAGE_LIST', data)
            });
        }
    },
    getConfig({ commit, state }) {
        if (!state.configs.length) {
            axiosGet(config).then(({data}) => {
                commit('CONFIG_LIST', data)
            });
        }
    },
    getProvider({ commit, state }) {
        axiosGet(brand_delivery_provider).then(({data}) => {
            commit('DELIVERY_PROVIDER', data)
        })
    },
    getNotificationChannels({commit, state}){
        if (!state.notification_channels.length){
            axiosGet(notification_channels).then(({data}) => {
                commit('NOTIFICATION_CHANNELS', data);
            });
        }
    },
    getNotificationEvents({commit, state, dispatch}, alias) {
        commit('SET_LOADER', true);
        dispatch('getNotificationChannels');
        axiosGet(`${notification_events}?type=${alias}&per_page=100`).then(({data}) => {
            commit('NOTIFICATION_EVENTS', data);
            commit('SET_LOADER', false);
        });
    },
    getStatuses({commit}, type) {
        axiosGet(`${statuses}/${type}`).then(res => {
            commit('SET_STATUSES', res.data)
        })
    }
};

const mutations = {
    LANGUAGE_LIST(state, data) {
        state.languages = data.map((lang) => {
            return {
                ...lang,
                id: lang.key,
                value: lang.title
            }
        });
    },
    CONFIG_LIST(state, data) {
        state.configs = data;
    },
    NOTIFICATION_EVENTS(state, data){
        state.notification_events = data.data;
    },
    NOTIFICATION_CHANNELS(state, data){
        state.notification_channels = data.map(channel => {
            return {
                ...channel,
                name: Vue.prototype.$t(channel.name),
                id: channel.name
            }
        });
    },
    SET_STATUSES(state, data) {
        state.statuses = Vue.prototype.collection(data)
            .shaper();
    },
    DELIVERY_PROVIDER(state, data) {
        state.provider = data.provider
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
