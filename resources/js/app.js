require('@/bootstrap');

// Vue
window.Vue = require('vue').default;

// Axios Interceptors
require('vue-axios-interceptors');

// Axios, Vue-Axios
import VueAxios from 'vue-axios';
import axios from 'axios';
window.axios = require('axios');
Vue.use(VueAxios, axios);

// Filters
require('@/mixins/Filters');

// Vue-Axios defaults
Vue.axios.defaults.withCredentials = true;

// Vue-Notifications
import Notifications from 'vue-notification';
Vue.use(Notifications);

// Vue-Router
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Vue-Moment
// Vue.use(require('vue-moment'));

// Loading indicator
// import LoadingIndicator from "@/components/ui/LoadingIndicator";
// Vue.component('LoadingIndicator', LoadingIndicator);

// // Separator
// import Separator from "@/components/ui/Separator";
// Vue.component('Separator', Separator);


// Store
import store from '@/config/store';

// Routes
import routes from '@/config/routes';
const router = new VueRouter({ mode: 'history', routes: routes});

// App component
import AppComponent from '@/App.vue';

// Mount App
const app = new Vue({
  mixins: [],
  components: { 
    AppComponent
  },
  router,
  store
}).$mount('#app');