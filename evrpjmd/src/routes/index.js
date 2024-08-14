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
  // admin - dmaster indikator kinerja
  {
    path: '/admin/dmaster/indikatorkinerja',
    name: 'DMasterIndikatorKinerja',
    meta: {
      title: 'DATA MASTER - INDIKATOR KINERJA',
    },
    component: () => import('@/pages/admin/dmaster/DMasterIndikatorKinerja.vue'),
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
  // admin - dmaster misi
  {
    path: '/admin/dmaster/misi',
    name: 'DMasterMisi',
    meta: {
      title: 'DATA MASTER - MISI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterMisi.vue'),
  },
  // admin - dmaster misi - kelola
  {
    path: '/admin/dmaster/misi/:RpjmdVisiID/manage',
    name: 'DMasterMisiManage',
    meta: {
      title: 'DATA MASTER - MISI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterMisiManage.vue'),
  },
  // admin - dmaster tujuan
  {
    path: '/admin/dmaster/tujuan',
    name: 'DMasterTujuan',
    meta: {
      title: 'DATA MASTER - TUJUAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterTujuan.vue'),
  },
  // admin - dmaster tujuan - kelola
  {
    path: '/admin/dmaster/tujuan/:RpjmdMisiID/manage',
    name: 'DMasterTujuanManage',
    meta: {
      title: 'DATA MASTER - TUJUAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterTujuanManage.vue'),
  },
  // admin - dmaster sasaran
  {
    path: '/admin/dmaster/sasaran',
    name: 'DMasterSasaran',
    meta: {
      title: 'DATA MASTER - SASARAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterSasaran.vue'),
  },
  // admin - dmaster sasaran - kelola
  {
    path: '/admin/dmaster/sasaran/:RpjmdTujuanID/manage',
    name: 'DMasterSasaranManage',
    meta: {
      title: 'DATA MASTER - SASARAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterSasaranManage.vue'),
  },
  // admin - dmaster sasaran
  {
    path: '/admin/dmaster/strategi',
    name: 'DMasterStrategi',
    meta: {
      title: 'DATA MASTER - STRATEGI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterStrategi.vue'),
  },
  // admin - dmaster sasaran - kelola
  {
    path: '/admin/dmaster/strategi/:RpjmdSasaranID/manage',
    name: 'DMasterStrategiManage',
    meta: {
      title: 'DATA MASTER - STRATEGI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterStrategiManage.vue'),
  },
  // admin - relation - indikator - tujuan
  {
    path: '/admin/relations/tujuan',
    name: 'DMasterRelationTujuan',
    meta: {
      title: 'RELAIION - INDIKATOR TUJUAN',
    },
    component: () => import('@/pages/admin/dmaster/RelationTujuanIndikator.vue'),
  },
  // admin - relation - indikator - tujuan
  {
    path: '/admin/relations/sasaran',
    name: 'DMasterRelationSasaran',
    meta: {
      title: 'RELAIION - INDIKATOR SASARAN',
    },
    component: () => import('@/pages/admin/dmaster/RelationSasaranIndikator.vue'),
  },
  // admin - relation - strategi - program
  {
    path: '/admin/relations/sasaran',
    name: 'DMasterRelationSasaran',
    meta: {
      title: 'RELAIION - INDIKATOR SASARAN',
    },
    component: () => import('@/pages/admin/dmaster/RelationSasaranIndikator.vue'),
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