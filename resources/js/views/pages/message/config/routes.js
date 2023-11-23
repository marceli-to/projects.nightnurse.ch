import MessageIndex from '@/views/pages/message/List.vue';
import MessageCreate from '@/views/pages/message/Create.vue';

const routes = [

  {
    name: 'messages',
    path: '/project/:slug/:uuid/:view?',
    component: MessageIndex,
  },

];

export default routes;