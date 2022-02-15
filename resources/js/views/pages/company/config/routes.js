import CompanyIndex from '@/views/pages/company/List.vue';
import CompanyForm from '@/views/pages/company/Form.vue';

const routes = [

  {
    name: 'companies',
    path: '/companies',
    component: CompanyIndex,
  },

  {
    name: 'company-create',
    path: '/company/create',
    component: CompanyForm,
  },
  
  {
    name: 'company-edit',
    path: '/company/edit/:id',
    component: CompanyForm,
  },

];

export default routes;