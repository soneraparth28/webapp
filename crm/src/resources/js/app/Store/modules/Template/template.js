import axios from 'axios'
import { app_templates } from '../../../config/apiUrl'
import { collection } from "../../../Helpers/helpers";

const state = {
    templates: {
        data: [],
    }
};

const actions = {
    getTemplates({commit, state}, t_url = app_templates) {
        commit('SET_LOADER', true);
        if (!state.templates.length) {
            axios.get(t_url).then(({data}) => {
                commit('SET_LOADER', false);
                commit("TEMPLATE_LIST", data);
            })
        }
    },
};

const mutations = {
    TEMPLATE_LIST(state, data) {

        if (state.templates.data.length) {
            state.templates.data = state.templates.data.concat(data);
        }else {
            state.templates.data = data;
        }
        // state.templates.next_page_url = data.next_page_url;
    },
};

const getters = {
    getTemplate: state => id => {
        if (state.templates.data.length)
            return collection(state.templates.data).find(id);
        return {}
    },
};


export default {
    state,
    getters,
    actions,
    mutations
}
