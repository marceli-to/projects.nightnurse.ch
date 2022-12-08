import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: false,
    recipients: [],
  },
  mutations: {
    user(state, user) {
      state.user = user;
    },
    recipients(state, recipients) {
      state.recipients = recipients;
    },
  }
});