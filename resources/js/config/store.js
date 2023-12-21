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
    markupFiles: [],
    markupMessage: null,
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

    hasUnlockedMarkUps(state, hasUnlockedMarkUps) {
      state.hasUnlockedMarkUps = hasUnlockedMarkUps;
    },

    markupMessage(state, markupMessage) {
      state.markupMessage = markupMessage;
    },

    // check and delete
    markupFiles(state, markupFiles) {
      state.markupFiles = markupFiles;
    },

    // check an delete
    hasMarkUps(state, hasMarkUps) {
      state.hasMarkUps = hasMarkUps;
    },

    // check and delete
    markup(state, markups) {
      state.markup = markups;
    },

  },
});