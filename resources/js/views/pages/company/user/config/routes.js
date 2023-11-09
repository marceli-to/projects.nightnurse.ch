import UserIndex from '@/views/pages/company/user/List.vue';
import UserCreate from '@/views/pages/company/user/Create.vue';
import UserUpdate from '@/views/pages/company/user/Update.vue';
import UserRegister from '@/views/pages/company/user/Register.vue';

import EmployeeIndex from '@/views/pages/company/employee/List.vue';
import EmployeeRegister from '@/views/pages/company/employee/Register.vue';
import EmployeeUpdate from '@/views/pages/company/employee/Update.vue';

const routes = [

  {
    name: 'users',
    path: '/companies/company/:companyUuid/users',
    component: UserIndex
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

  {
    name: 'user-register',
    path: '/companies/company/:companyUuid/users/user/register',
    component: UserRegister,
  },

  {
    name: 'employees',
    path: '/employees',
    component: EmployeeIndex,
  },

  {
    name: 'employee-register',
    path: '/employee/register',
    component: EmployeeRegister,
  },

  {
    name: 'employee-update',
    path: '/employee/update/:uuid',
    component: EmployeeUpdate,
  },

];

export default routes;