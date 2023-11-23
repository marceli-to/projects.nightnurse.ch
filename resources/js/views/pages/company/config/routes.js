import CompanyIndex from '@/views/pages/company/List.vue';
import CompanyCreate from '@/views/pages/company/Create.vue';
import CompanyUpdate from '@/views/pages/company/Update.vue';
import CompanyUsersIndex from '@/views/pages/company/Users.vue';

const routes = [

  {
    name: 'companies',
    path: '/companies',
    component: CompanyIndex,
  },

  {
    name: 'companies-users',
    path: '/companies/users',
    component: CompanyUsersIndex,
  },

  {
    name: 'company-create',
    path: '/companies/company/create',
    component: CompanyCreate,
  },
  
  {
    name: 'company-update',
    path: '/companies/company/update/:uuid',
    component: CompanyUpdate,
  },
];

export default routes;