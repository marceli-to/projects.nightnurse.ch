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

// Vue-Axios defaults
Vue.axios.defaults.withCredentials = true;

Vue.use(require('vue-moment'));

// Filters
require('@/mixins/Filters');


// Vue-Notifications
import Notifications from 'vue-notification';
Vue.use(Notifications);

// Vue-Router
import VueRouter from 'vue-router';
Vue.use(VueRouter);


// Store
import store from '@/config/store';

// Routes
import baseRoutes from '@/config/routes';
import projectRoutes from '@/views/pages/project/config/routes';
import companyRoutes from '@/views/pages/company/config/routes';
import userRoutes from '@/views/pages/company/user/config/routes';
import messageRoutes from '@/views/pages/message/config/routes';
import profileRoutes from '@/views/pages/profile/config/routes';

const router = new VueRouter(
  { 
    mode: 'history', 
    scrollBehavior (to, from, savedPosition) {
      return { x: 0, y: 0 }
    },
    routes: [
      ...baseRoutes,
      ...projectRoutes,
      ...companyRoutes,
      ...userRoutes,
      ...messageRoutes,
      ...profileRoutes
    ]
  }
);

// App component
import AppComponent from '@/App.vue';

// Mount App
if (document.querySelector('#app')) {
  const app = new Vue({
    mixins: [],
    components: { 
      AppComponent
    },
    router,
    store,
    
  }).$mount('#app');
}
