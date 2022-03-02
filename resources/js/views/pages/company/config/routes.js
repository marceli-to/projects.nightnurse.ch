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