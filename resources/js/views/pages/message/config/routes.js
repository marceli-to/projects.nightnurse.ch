import MessageIndex from '@/views/pages/message/List.vue';
import MessageCreate from '@/views/pages/message/Create.vue';

const routes = [

  {
    name: 'messages',
    // path: '/projects/project/:uuid/messages/:message?',
    path: '/projects/project/:uuid/messages/:view?',
    component: MessageIndex,
  },

  // {
  //   name: 'messages-feedback',
  //   path: '/projects/project/:uuid/feedback',
  //   component: MessageIndex,
  // },

  {
    name: 'message-create',
    path: '/projects/project/:uuid/message/create',
    component: MessageCreate,
  },

];

export default routes;