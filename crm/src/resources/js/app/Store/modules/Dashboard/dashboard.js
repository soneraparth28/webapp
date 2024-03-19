import { axiosGet } from '../../../Helpers/AxiosHelper'
import {
    brand_subscriber_count,
    subscriber_count,
    campaign_count,
    brand_campaign_count,
    brand_count,
    email_statics,
    brand_email_statics,
    brand_segment_count,
    brand_sns_subscriptions,
    campaigns, sns_subscription
} from "../../../config/apiUrl";

const state = {
    subscribers: {},
    total_unsubscribers: 0,
    total_campaigns: {},
    total_brands: 0,
    total_segments: 0,
    email_statics: {},
    subscriptions: [],
    loading_email_statistics: true,
    loading_subscriber_statistics: true,
    loading_campaign_count: true
};

const actions = {
    getTotalSubscriber({commit, state}, payload = {range: 1}) {
        if (!payload.subscribers_count)
            return;
        commit('SET_LOADING_SUBSCRIBER_STATISTICS', true);
        commit('SET_SUBSCRIBER_COUNT', []);
        let url = payload.alias ? brand_subscriber_count : subscriber_count;
        url = payload.range ? `${url}/${payload.range}` : url;
        axiosGet(url).then(response => {
            commit('SET_SUBSCRIBER_COUNT', response.data)
            commit('SET_LOADING_SUBSCRIBER_STATISTICS', false);
        })
    },
    getTotalCampaign({commit, state}, payload = null) {
        if (!payload.campaigns_count)
            return
        commit('SET_CAMPAIGN_LOADING_COUNT', true);
        const url = payload.alias ? brand_campaign_count : campaign_count;
        axiosGet(`${url}/1`).then(response => {
            commit('SET_CAMPAIGN_COUNT', response.data)
            commit('SET_CAMPAIGN_LOADING_COUNT', false);
        })
    },
    getTotalBrands({commit, state}) {
        axiosGet(brand_count).then(response => {
            commit('SET_BRAND_COUNT', response.data)
        })
    },
    getEmailStatics({commit, state}, payload = {}) {
        if (!payload.email_statistics)
            return;
        commit('SET_LOADING_EMAIL_STATISTICS', true);
        commit('SET_EMAIL_STATICS', {})
        let url = payload.alias ? brand_email_statics : email_statics;
        url = payload.range ? `${url}/${payload.range}` : url;
        axiosGet(url).then(response => {
            commit('SET_EMAIL_STATICS', response.data)
            commit('SET_LOADING_EMAIL_STATISTICS', false);
        })
    },
    getTotalSegments({commit, state}) {
        axiosGet(brand_segment_count).then(response => {
            commit('SET_SEGMENT_COUNT', response.data)
        })
    },

    getSubscriptions({commit}, payload = {}) {
        const url = payload.alias === 'app' ? sns_subscription : brand_sns_subscriptions;
        axiosGet(url).then(response => {
            commit('SET_SUBSCRIPTION', response.data);
        })
    },

    getSingleCampaignEmailStatistics({commit}, payload){
        commit('SET_LOADING_EMAIL_STATISTICS', true);
        commit('SET_EMAIL_STATICS', {})
        let url = payload.type ? `${campaigns}/${payload.id}/email-statistics/${payload.type}`: `${campaigns}/${payload.id}/email-statistics`;
        axiosGet(url).then(response => {
            commit('SET_EMAIL_STATICS', response.data)
            commit('SET_LOADING_EMAIL_STATISTICS', false);
        })
    },

    dispatchAppDashboard({dispatch}, permission) {
        dispatch('getEmailStatics', { range: 1, ...permission });
        dispatch('getTotalBrands', permission);
        dispatch('getTotalCampaign', permission);
        dispatch('getTotalSubscriber', { range: 1, ...permission });
        dispatch('getEmailStats', {alias: false, ...permission});
        dispatch('getSubscriberStats', {alias: false, ...permission});
        dispatch('getSubscriptions',{alias: 'app', ...permission});
    },
    dispatchBrandDashboard({dispatch}, permission) {
        dispatch('getEmailStatics', {alias: true, range: 1, ...permission});
        dispatch('getTotalCampaign', {alias: true, ...permission});
        dispatch('getTotalSubscriber', {alias: true, range: 1, ...permission});
        dispatch('getTotalSegments', permission);
        dispatch('getSubscriptions',{alias: 'brand', ...permission});
        dispatch('getEmailStats', {alias: true, ...permission})
        dispatch('getSubscriberStats', {alias: true, ...permission})
    }
};

const mutations = {
    SET_SUBSCRIBER_COUNT(state, data) {
        state.subscribers = data;
    },
    SET_CAMPAIGN_COUNT(state, data) {
        state.total_campaigns = data;
    },
    SET_BRAND_COUNT(state, data) {
        state.total_brands = data;
    },
    SET_EMAIL_STATICS(state, data) {
        state.email_statics = data;
    },
    SET_SEGMENT_COUNT(state, data) {
        state.total_segments = data;
    },
    SET_SUBSCRIPTION(state, data) {
        state.subscriptions = data;
    },
    SET_LOADING_EMAIL_STATISTICS(state, data) {
        state.loading_email_statistics = data;
    },
    SET_LOADING_SUBSCRIBER_STATISTICS(state, data) {
        state.loading_subscriber_statistics = data;
    },
    SET_CAMPAIGN_LOADING_COUNT(state, data){
        state.loading_campaign_count = data;
    }
};

const getters = {

};


export default {
    state,
    getters,
    actions,
    mutations
}
