import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: false,
    hasMarkUps: false,
    hasUnlockedMarkUps: false,
    feedType: 'public',
    recipients: [],
    reactionTypes: [],
    markup: {}, 
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
    },
    
    userListIsGrouped(state, userListIsGrouped) {
      state.userListIsGrouped = userListIsGrouped;
    },

    hasMarkUps(state, hasMarkUps) {
      state.hasMarkUps = hasMarkUps;
    },

    hasUnlockedMarkUps(state, hasUnlockedMarkUps) {
      state.hasUnlockedMarkUps = hasUnlockedMarkUps;
    },

    markup(state, markups) {
      state.markup = markups;
    },

  },
});