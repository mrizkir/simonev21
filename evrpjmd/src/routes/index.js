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
    path: '/admin/:token',
    name: 'AdminDashboard',
    meta: {
      title: 'DASHBOARD EVALUASI RPJMD',
    },
    component: () => import('@/pages/admin/DashboardAdmin.vue'),
  },
  // admin - dmaster periode rpjmd
  {
    path: '/admin/dmaster/perioderpjmd',
    name: 'DMasterPeriodeRPJMD',
    meta: {
      title: 'DATA MASTER - PERIODE RPJMD',
    },
    component: () => import('@/pages/admin/dmaster/DMasterPeriodeRPJMD.vue'),
  },
  // admin - dmaster indikator program
  {
    path: '/admin/dmaster/indikatorprogram',
    name: 'DMasterIndikatorKinerja',
    meta: {
      title: 'DATA MASTER - INDIKATOR PROGRAM',
    },
    component: () => import('@/pages/admin/dmaster/DMasterIndikatorProgram.vue'),
  },
  // admin - dmaster visi
  {
    path: '/admin/dmaster/visi',
    name: 'DMasterVisi',
    meta: {
      title: 'DATA MASTER - VISI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterVisi.vue'),
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