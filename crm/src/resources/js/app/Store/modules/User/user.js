import { axiosGet } from '../../../Helpers/AxiosHelper'
import {brand_selectable_users, users} from '../../../config/apiUrl'
import {collection} from "../../../Helpers/helpers";

const state = {
    users: []
};

const actions = {
    getUsers({commit, state}, payload = {}) {
        const user_url = payload.alias === 'brand' ? brand_selectable_users : users;
        const url = payload.users ? `${user_url}?existing=${payload.users}` : user_url;
        axiosGet(url).then(({data}) => {
            commit('SET_USERS', data)
        })
    },
    removeUser({commit, state}, payload){
        commit('SET_USERS', collection(state.users).removeObject(payload));
    },
    addUser({commit, state}, user){
        commit('SET_USERS', state.users.concat([user]));
    }
};

const mutations = {
    SET_USERS(state, data) {
        state.users = data;
    }
};

const getters = {
    getFormattedUsers: state => {
        if (state.users) {
            return state.users.map(user => {
                user.value = user.full_name;
                return user;
            });
        }
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
