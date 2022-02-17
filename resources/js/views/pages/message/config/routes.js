import MessageIndex from '@/views/pages/message/List.vue';
import MessageCreate from '@/views/pages/message/Create.vue';

const routes = [

  {
    name: 'messages',
    path: '/projects/project/:uuid/messages',
    component: MessageIndex,
  },

  {
    name: 'message-create',
    path: '/projects/project/:uuid/message/create',
    component: MessageCreate,
  },

];

export default routes;