import ProjectIndex from '@/views/pages/project/List.vue';
import ProjectCreate from '@/views/pages/project/Create.vue';
import ProjectUpdate from '@/views/pages/project/Update.vue';

const routes = [

  {
    name: 'projects',
    path: '/projects',
    component: ProjectIndex,
  },

  {
    name: 'project-create',
    path: '/projects/project/create',
    component: ProjectCreate,
  },
  
  {
    name: 'project-update',
    path: '/projects/project/update/:uuid',
    component: ProjectUpdate,
  },
];

export default routes;