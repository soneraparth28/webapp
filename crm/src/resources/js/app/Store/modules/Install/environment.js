const state = {
    environment: {}
};

const actions = {
    setEnvironment({commit}, payload){
        commit('SET_ENVIRONMENT', payload);
    }
};

const mutations = {
    SET_ENVIRONMENT(state, payload) {
        state.environment = payload;
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
