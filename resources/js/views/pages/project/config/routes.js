import ProjectIndex from '@/views/pages/project/List.vue';
import ProjectCreate from '@/views/pages/project/Create.vue';
import ProjectUpdate from '@/views/pages/project/Update.vue';
import ProjectAccess from '@/views/pages/project/ManageAccess.vue';

const routes = [

  {
    name: 'home',
    path: '/',
    component: ProjectIndex,
  },
  {
    name: 'projects',
    path: '/projects/:type?',
    component: ProjectIndex,
  },

  {
    name: 'project-create',
    path: '/project/create',
    component: ProjectCreate,
  },
  
  {
    name: 'project-update',
    path: '/project/update/:slug/:uuid/:redirect?',
    component: ProjectUpdate,
  },

  {
    name: 'project-access',
    path: '/project/access/:slug/:uuid',
    component: ProjectAccess,
  },
];

export default routes;