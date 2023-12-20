import MarkupIndex from '@/views/pages/markup/Index.vue';

const routes = [

  {
    name: 'markup',
    path: '/project/:slug/:uuid/markup/:messageUuid/:imageUuid',
    component: MarkupIndex,
  },

];

export default routes;