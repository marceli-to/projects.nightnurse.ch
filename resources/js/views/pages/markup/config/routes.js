import MarkupIndex from '@/views/pages/markup/Index.vue';

const routes = [

  {
    name: 'markup',
    path: '/project/:slug/:uuid/markup/:fileUuid',
    component: MarkupIndex,
  },

];

export default routes;