import {axiosGet} from "../../../Helpers/AxiosHelper";
import {lists, selectable_list} from "../../../config/apiUrl";


const state = {
    lists: []
}


const mutations = {
    LISTS(state, data) {
        state.lists = data;
    }
}

const actions = {
    getLists({commit, state}, l_url = lists, per_page = 500) {
        commit('SET_LOADER', true)
        if (!state.lists.length) {
            axiosGet(`${l_url}?per_page=${per_page}`).then(({data}) => {
                commit('SET_LOADER', false);
                commit("LISTS", data);
            })
        }
    },

    getSelectableLists({commit, state}, payload) {
        axiosGet(selectable_list).then(({data}) => {
            commit('LISTS', data)
        })
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
