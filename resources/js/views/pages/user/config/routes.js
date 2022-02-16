import UserIndex from '@/views/pages/user/List.vue';
import UserCreate from '@/views/pages/user/Create.vue';
import UserUpdate from '@/views/pages/user/Update.vue';

const routes = [

  {
    name: 'users',
    path: '/users',
    component: UserIndex,
  },

  {
    name: 'user-create',
    path: '/users/user/create',
    component: UserCreate,
  },
  
  {
    name: 'user-update',
    path: '/users/user/update/:uuid',
    component: UserUpdate,
  },

];

export default routes;