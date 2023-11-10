import ProjectIndex from '@/views/pages/project/List.vue';
import ProjectArchive from '@/views/pages/project/List.vue';
import ProjectCreate from '@/views/pages/project/Create.vue';
import ProjectUpdate from '@/views/pages/project/Update.vue';

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
    path: '/project/update/:slug/:redirect?',
    component: ProjectUpdate,
  },
];

export default routes;