import MessageIndex from '@/views/pages/message/List.vue';

const routes = [

  {
    name: 'messages',
    path: '/project/:slug/:uuid/:view?',
    component: MessageIndex,
  },

];

export default routes;