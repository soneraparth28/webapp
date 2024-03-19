import { axiosGet } from '../../../Helpers/AxiosHelper'
import { logic_names, logic_operators, custom_field } from '../../../config/apiUrl'
import {collection} from "../../../Helpers/helpers";
import Vue from 'vue'

const state = {
    operators: [],
    logics: [],
        custom_fields: []
};

const actions = {
    getOperators({commit}) {
        axiosGet(logic_operators)
            .then(response => commit('SET_OPERATORS', response.data));
    },
    /*getLogics({commit}) {
        axiosGet(logic_names)
            .then(response => commit('SET_LOGICS', response.data))
    },*/
    getSubscriberCustomFields({commit}) {
        commit('SET_LOADER', true)
        axiosGet(custom_field).then(response => {
            commit('SET_CUSTOM_FIELDS', response.data)
            commit('SET_LOADER', false)
        })
    },
    dispatchAllAction({dispatch}) {
        dispatch('getOperators')
    }
};

const mutations = {
    SET_OPERATORS(state, data) {
        state.operators = data;
    },
    SET_LOGICS(state, data) {
        state.logics = data;
    },
    SET_CUSTOM_FIELDS(state, data) {
        state.custom_fields = data;
    }
};

const getters = {
    getFormattedCustomFields: state => {
        return collection(state.custom_fields)
            .shaper('name', 'name')
    },
    getFormattedLogics: (state, getters) => {
        return collection(state.logics)
            .shaper('translated_name', 'name')
            .concat([{"id":  'disabled', value:  Vue.prototype.$t('custom_fields'), disabled: true}])
            .concat(getters.getFormattedCustomFields)
    },
    getFormattedOperators: state => {
        return collection(state.operators)
            .shaper('translated_name', 'name')
    }
};


export default {
    state,
    getters,
    actions,
    mutations
}
