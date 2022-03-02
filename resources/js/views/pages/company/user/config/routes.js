import UserIndex from '@/views/pages/company/user/List.vue';
import UserCreate from '@/views/pages/company/user/Create.vue';
import UserUpdate from '@/views/pages/company/user/Update.vue';

const routes = [

  {
    name: 'users',
    path: '/companies/company/:companyUuid/users',
    component: UserIndex,
  },

  {
    name: 'user-create',
    path: '/companies/company/:companyUuid/users/user/create',
    component: UserCreate,
  },
  
  {
    name: 'user-update',
    path: '/companies/company/:companyUuid/users/user/update/:uuid',
    component: UserUpdate,
  },

];

export default routes;