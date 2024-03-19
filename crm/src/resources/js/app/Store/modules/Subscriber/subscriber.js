import {selectable_subscribers} from "../../../config/apiUrl";
import {axiosGet} from "../../../Helpers/AxiosHelper";


const state = {
    subscribers: []
}

const actions = {
    selectableSubscribers({commit, state}, payload) {
        if (!Object.keys(state.subscribers).length) {
            axiosGet(selectable_subscribers).then(({data}) => {
                commit('SUBSCRIBERS', data)
            })
        }
    }
}

const mutations = {
    SUBSCRIBERS(state, data) {
        state.subscribers = data
    }
}

const getters = {

}

export default {
    actions,
    state,
    mutations,
    getters
}
