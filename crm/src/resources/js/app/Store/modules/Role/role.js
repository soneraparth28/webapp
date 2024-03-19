import { axiosGet } from '../../../Helpers/AxiosHelper'
import {roles, brand_app_roles, brand_selectable_roles} from '../../../config/apiUrl'
import {optional} from "../../../Helpers/helpers";

const state = {
    roles: []
};

const actions = {
    getRoles({commit, state}, payload) {
        commit('SET_LOADER', true)
        const url = payload.alias === 'brand' ? brand_selectable_roles : roles;
        axiosGet(url).then(({data}) => {
            commit('SET_LOADER', false)
            commit('SET_ROLES', data)
        })
    },
    getAllRoles({commit, state}, brand_id = null) {
        commit('SET_LOADER', true)
        axiosGet(`${brand_app_roles}${brand_id ? '?brand_id='+brand_id : ''}`).then(({data}) => {
            commit('SET_LOADER', false)
            commit('SET_ROLES', data)
        })
    }
};

const mutations = {
    SET_ROLES(state, data) {
        state.roles = data;
    }
};

const getters = {
    getFormattedRoles: state => {
        if (state.roles) {
            return state.roles.map(role => {
                if (role.brand_id) {
                    role.value = `${role.name}(${optional(role, 'brand', 'name')})`;
                }else {
                    role.value = role.name;
                }
                return role;
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
