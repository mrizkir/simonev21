import { createWebHistory, createRouter } from 'vue-router'
import NotFoundComponent from '@/components/NotFoundComponent.vue';

const routes = [
  {
    path: '/',
    name: 'FrontDashboard',
    meta: {
      title: 'DASHBOARD EVALUASI RPJMD',
    },
    component: () => import('@/pages/DashboardFront.vue'),
  },
  {
    path: '/login',
    name: 'FrontLogin',
    meta: {
      title: 'LOGIN',
    },
    component: () => import('@/pages/Login.vue'),
  },
  // admin dashboard
  {
    path: '/admin',
    name: 'AdminDashboard',
    meta: {
      title: 'DASHBOARD EVALUASI RPJMD',
    },
    component: () => import('@/pages/admin/DashboardAdmin.vue'),
  },
  // admin - dmaster
  {
    path: '/admin/dmaster',
    name: 'DMasterDashboard',
    meta: {
      title: 'DATA MASTER',
    },
    component: () => import('@/pages/admin/dmaster/DMaster.vue'),
  },
  {
    path: '/admin/dmaster/indikatorkinerja',
    name: 'DMasterIndikatorKinerja',
    meta: {
      title: 'DATA MASTER - INDIKATOR KINERJA',
    },
    component: () => import('@/pages/admin/dmaster/DMasterIndikatorKinerja.vue'),
  },
  // other page
  {
    path: '/404',
    name: 'NotFoundComponent',
    meta: {
      title: 'PAGE NOT FOUND',
    },
    component: NotFoundComponent,
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/404',
  },
]

const route = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

route.beforeEach((to, from, next) => {
  document.title = to.meta.title
  next()
})

export default route;