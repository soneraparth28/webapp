import './bootstrap';
import './plugins';
import Vue from 'vue';
import vuexI18n from 'vuex-i18n';
import './core/coreApp';
import './app/appComponent'
import './app/Helpers/helpers'
import appVuexStore from './app/Store'
window.Vue = Vue;



/**
 * localization
 * $t('key') or this.$('key')
 * link: https://github.com/dkfbasel/vuex-i18n
 * */

Vue.use(vuexI18n.plugin, appVuexStore);

// add translations directly to the application
let language = JSON.parse(window.localStorage.getItem('app-languages'));
let key = window.localStorage.getItem('app-language');
Vue.i18n.add(key, language);
// set the start locale to use
Vue.i18n.set(key);
import './app/Helpers/translator';
/*------ localization end ------*/

const app = new Vue({
    store: appVuexStore,
    el: '#app',
});
