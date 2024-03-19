import {notification_events} from "../../../config/apiUrl";
import {axiosGet} from "../../../Helpers/AxiosHelper";

const state = {
    notification_event: {}
};

const actions = {
    getNotificationEvent({commit, dispatch}, payload) {
        commit('SET_LOADER', true);
        axiosGet(`${notification_events}/${payload}`).then(res => {
            commit('SET_NOTIFICATION_EVENT', res.data)
            commit('SET_LOADER', false);
            dispatch('getNotificationChannels')
        });
    }
};

const mutations = {
    SET_NOTIFICATION_EVENT(state, data) {
        state.notification_event = data;
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
