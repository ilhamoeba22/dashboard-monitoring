import { createRouter, createWebHistory } from 'vue-router';

// Dummy router untuk kompatibilitas dengan Materio Layouts di dalam Inertia SPA
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/:pathMatch(.*)*',
      name: 'CatchAll',
      component: { template: '<div></div>' }
    }
  ],
});

export default function (app) {
  app.use(router);
}
