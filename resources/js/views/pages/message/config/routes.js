import MessageIndex from '@/views/pages/message/List.vue';
import MessageCreate from '@/views/pages/message/Create.vue';

const routes = [

  {
    name: 'messages',
    // path: '/projects/project/:uuid/messages/:view?',
    path: '/project/:slug/messages/:view?',
    component: MessageIndex,
  },

];

export default routes;