import {axiosGet} from "../../../Helpers/AxiosHelper";
import { app_general_settings } from '../../../config/apiUrl'
import {formatted_date, formatted_time} from "../../../Helpers/helpers";

const state = {
    settings: {},
    darkMode: false,
    dateFormat: formatted_date(),
    timeFormat: parseInt(formatted_time()),
    ...window.settings
};

const actions = {
    getSettings({commit, state}) {
        axiosGet(app_general_settings).then(({ data }) => {
            commit('APP_GENERAL_SETTINGS_LIST', data)
        });
    }
};

const mutations = {
    APP_GENERAL_SETTINGS_LIST(state, data) {
        state.settings = data;
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
