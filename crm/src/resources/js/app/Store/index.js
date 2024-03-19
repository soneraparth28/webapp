import Vue from 'vue'
import Vuex from 'vuex'
import additional from './modules/General/additional'
import settings  from './modules/Settings/settings'
import notification from "./modules/Settings/notification"
import user from './modules/User/user'
import role from "./modules/Role/role";
import template from "./modules/Template/template";
import custom_field from "./modules/CustomField/custom_field";
import Example from './modules/Example'
import lists from "./modules/Lists/lists";
import dashboard from "./modules/Dashboard/dashboard";
import segment from "./modules/Segment/segment";
import subscriber from "./modules/Subscriber/subscriber";
import campaign from "./modules/Campaign/campaign";
import profile from "./modules/Profile/profile";
import stats from "./modules/Email/stats";
import notification_event from "./modules/Settings/notification_event";
import environment from "./modules/Install/environment";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        loading: false,
        theme: {
            darkMode: false
        },
        brand: window.brand
    },
    mutations: {
        SET_LOADER(state, data){
            state.loading = data;
        }
    },
    modules: {
        additional,
        settings,
        notification,
        user,
        role,
        template,
        lists,
        custom_field,
        dashboard,
        segment,
        subscriber,
        campaign,
        profile,
        stats,
        notification_event,
        environment,
        Example
    }
});
