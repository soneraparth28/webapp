import {axiosGet} from "../../../Helpers/AxiosHelper";
import {selectable_campaigns} from "../../../config/apiUrl";


const state = {
    campaigns: []
}

const actions = {
    getCampaigns({commit}, type = 'selectable') {
        axiosGet(selectable_campaigns)
            .then(({data}) => commit('GET_CAMPAIGNS', data))
    }
}

const mutations = {
    GET_CAMPAIGNS(state, data) {
        state.campaigns = data
    }
}

const getters = {

}

export default {
    state,
    actions,
    mutations,
    getters
}
