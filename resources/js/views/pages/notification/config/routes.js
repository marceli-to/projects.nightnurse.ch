import NotificationIndex from '@/views/pages/notification/List.vue';
import NotificationCreate from '@/views/pages/notification/Create.vue';
import NotificationUpdate from '@/views/pages/notification/Update.vue';

const routes = [

  {
    name: 'notifications',
    path: '/notifications',
    component: NotificationIndex,
  },

  {
    name: 'notification-create',
    path: '/notifications/notification/create',
    component: NotificationCreate,
  },
  
  {
    name: 'notification-update',
    path: '/notifications/notification/update/:id',
    component: NotificationUpdate,
  },
];

export default routes;