import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: false,
    feedType: 'public',
    recipients: [],
    reactionTypes: [],
  },
  mutations: {
    user(state, user) {
      state.user = user;
    },
    recipients(state, recipients) {
      state.recipients = recipients;
    },
    reactionTypes(state, reactionTypes) {
      state.reactionTypes = reactionTypes;
    },
    feedType(state, feedType) {
      state.feedType = feedType;
    }
  }
});