import {
    brand_email_statics,
    brand_subscriber_count,
    campaigns,
    email_statics,
    subscriber_count
} from "../../../config/apiUrl";
import {axiosGet} from "../../../Helpers/AxiosHelper";

const state = {
    campaign_stats: {},
    email_gross_stats: {},
    subscriber_stats: {}
};

const actions = {
    getCampaignStats({commit}, payload) {
        axiosGet(`${campaigns}/${payload}/email-statistics/gross`)
            .then(response => commit('SET_STATS', response.data))
    },
    getEmailStats({commit}, payload){
        if (!payload.email_statistics)
            return;
        let url = payload.alias ? brand_email_statics : email_statics;
        axiosGet(`${url}/gross`).then(response => commit('SET_EMAIL_GROSS_STATS', response.data))
    },
    getSubscriberStats({commit}, payload) {
        if (!payload.subscribers_count)
            return;
        let url = payload.alias ? brand_subscriber_count : subscriber_count;
        axiosGet(`${url}/gross`).then(response => {
            commit('SET_SUBSCRIBER_STATS', response.data)
        })
    }
};

const mutations = {
    SET_STATS(state, data) {
        state.campaign_stats = data;
    },
    SET_EMAIL_GROSS_STATS(state, data){
        state.email_gross_stats = data;
    },
    SET_SUBSCRIBER_STATS(state, data){
        state.subscriber_stats = data;
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
