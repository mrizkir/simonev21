import { createWebHistory, createRouter } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'FrontDashboard',
    meta: {
      title: 'DASHBOARD EVALUASI RPJMD',
    },
    component: () => import('@/pages/DashboardFront.vue'),
  },
]

const route = createRouter({
  history: createWebHistory(),
  routes,
})
route.beforeEach((to, from, next) => {
  document.title = to.meta.title;
  next();
})

export default route;