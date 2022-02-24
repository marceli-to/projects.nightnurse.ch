import ErrorForbidden from '@/views/errors/Forbidden.vue';
import ErrorNotFound from '@/views/errors/NotFound.vue';

// Dashboard
import Dashboard from '@/views/pages/Index.vue';

const routes = [

  // // Home
  // {
  //   name: 'home',
  //   path: '/',
  //   component: Dashboard,
  // },

  // // Dashboard
  // {
  //   name: 'dashboard',
  //   path: '/dashboard',
  //   component: Dashboard,
  // },

  // Authorization
  {
    name: 'logout',
    path: '/logout',
  },
  {
    name: 'forbidden',
    path: '/forbidden',
    component: ErrorForbidden,
  },
  {
    name: 'not-found',
    path: '/not-found',
    component: ErrorNotFound,
  }
];

export default routes;