import Vuex from 'vuex';
import newsModule from './modules/news';

const modules = {
    news: newsModule,

};

const state = {
    csrfToken: '',
    user: null,
};

const getters = {
    isAuthenticated: (state) => !!state.user,
    loggedUser: (state) => state.user,
    csrf: ({ csrfToken }) => csrfToken,
};

const actions = {
    nuxtServerInit({ commit }) {
        return this.$api.getUser().then(r => {
            commit('setUser', r.result);
        });
    },
    nuxtClientInit({ dispatch }) {
    },
    can(ctx, signature) {
        return this.$can(signature);
    },
};

const mutations = {
    setUser(state, user) {
        state.user = user || null;
    },
};

const createStore = () => new Vuex.Store({
    modules,
    state,
    getters,
    actions,
    mutations,
});

export default createStore;
