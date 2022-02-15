import CompanyIndex from '@/views/pages/company/List.vue';
import CompanyCreate from '@/views/pages/company/Create.vue';
import CompanyUpdate from '@/views/pages/company/Update.vue';

const routes = [

  {
    name: 'companies',
    path: '/companies',
    component: CompanyIndex,
  },

  {
    name: 'company-create',
    path: '/company/create',
    component: CompanyCreate,
  },
  
  {
    name: 'company-update',
    path: '/company/update/:uuid',
    component: CompanyUpdate,
  },

];

export default routes;