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
  // admin - dmaster strategi
  {
    path: '/admin/dmaster/strategi',
    name: 'DMasterStrategi',
    meta: {
      title: 'DATA MASTER - STRATEGI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterStrategi.vue'),
  },
  // admin - dmaster strategi - kelola
  {
    path: '/admin/dmaster/strategi/:RpjmdSasaranID/manage',
    name: 'DMasterStrategiManage',
    meta: {
      title: 'DATA MASTER - STRATEGI',
    },
    component: () => import('@/pages/admin/dmaster/DMasterStrategiManage.vue'),
  },
  // admin - dmaster arah kebijakan
  {
    path: '/admin/dmaster/arahkebijakan',
    name: 'DMasterArahKebijakan',
    meta: {
      title: 'DATA MASTER - ARAH KEBIJAKAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterArahKebijakan.vue'),
  },
  // admin - dmaster arah kebijakan - kelola
  {
    path: '/admin/dmaster/arahkebijakan/:RpjmdStrategiID/manage',
    name: 'DMasterArahKebijakanManage',
    meta: {
      title: 'DATA MASTER - ARAH KEBIJAKAN',
    },
    component: () => import('@/pages/admin/dmaster/DMasterArahKebijakanManage.vue'),
  },
  // admin - relation - arah kebijakan - program
  {
    path: '/admin/relations/programarahkebijakan',
    name: 'RelationProgramArahKebijakan',
    meta: {
      title: 'RELATION - PROGRAM ARAH KEBIJAKAN',
    },
    component: () => import('@/pages/admin/relations/RelationProgramArahKebijakan.vue'),
  },
  // admin - relation - arah kebijakan - program - manage
  {
    path: '/admin/relations/programarahkebijakan/:RpjmdArahKebijakanID/manage',
    name: 'RelationProgramArahKebijakanManage',
    meta: {
      title: 'RELATION - PROGRAM ARAH KEBIJAKAN',
    },
    component: () => import('@/pages/admin/relations/RelationProgramArahKebijakanManage.vue'),
  },
  // admin - relation - indikator - tujuan
  {
    path: '/admin/relations/indikatortujuan',
    name: 'RelationTujuan',
    meta: {
      title: 'RELATION - INDIKATOR TUJUAN',
    },
    component: () => import('@/pages/admin/relations/RelationTujuanIndikator.vue'),
  },
  // admin - relation - indikator - tujuan
  {
    path: '/admin/relations/indikatorsasaran',
    name: 'RelationSasaran',
    meta: {
      title: 'RELATION - INDIKATOR SASARAN',
    },
    component: () => import('@/pages/admin/relations/RelationSasaranIndikator.vue'),
  },  
  // admin - relation - indikator - program
  {
    path: '/admin/relations/indikatorprogram',
    name: 'RelationIndikatorProgram',
    meta: {
      title: 'RELATION - INDIKATOR PROGRAM',
    },
    component: () => import('@/pages/admin/relations/RelationProgramIndikator.vue'),
  },
  // admin - relation - pagu - program
  {
    path: '/admin/relations/paguprogram',
    name: 'RelationPaguProgram',
    meta: {
      title: 'RELATION - PAGU PROGRAM',
    },
    component: () => import('@/pages/admin/relations/RelationProgramPagu.vue'),
  },

  // admin - realitation - tujuan
  {
    path: '/admin/realitation/indikatortujuan',
    name: 'RealitationIndikatorTujuan',
    meta: {
      title: 'REALITATION - INDIKATOR TUJUAN',
    },
    component: () => import('@/pages/admin/realitation/RealitationIndikatorTujuan.vue'),
  },
  // admin - realitation - sasaran
  {
    path: '/admin/realitation/indikatorsasaran',
    name: 'RealitationIndikatorSasaran',
    meta: {
      title: 'REALITATION - INDIKATOR SASARAN',
    },
    component: () => import('@/pages/admin/realitation/RealitationIndikatorSasaran.vue'),
  },
  // admin - realitation - program
  {
    path: '/admin/realitation/indikatorprogramperurusan',
    name: 'RealitationIndikatorProgramPerUrusan',
    meta: {
      title: 'REALITATION - INDIKATOR PROGRAM PER URUSAN',
    },
    component: () => import('@/pages/admin/realitation/RealitationProgramIndikatorPerUrusan.vue'),
  },
  {
    path: '/admin/realitation/paguprogramperurusan',
    name: 'RealitationPaguProgramPerUrusan',
    meta: {
      title: 'REALITATION - PAGU PROGRAM PER URUSAN',
    },
    component: () => import('@/pages/admin/realitation/RealitationProgramPaguPerUrusan.vue'),
  },
  {
    path: '/admin/realitation/indikatorprogramperopd',
    name: 'RealitationIndikatorProgramPerOPD',
    meta: {
      title: 'REALITATION - INDIKATOR PROGRAM PER OPD',
    },
    component: () => import('@/pages/admin/realitation/RealitationProgramIndikatorPerOPD.vue'),
  },
  {
    path: '/admin/realitation/paguprogramperopd',
    name: 'RealitationPaguProgramPerOPD',
    meta: {
      title: 'REALITATION - PAGU PROGRAM PER OPD',
    },
    component: () => import('@/pages/admin/realitation/RealitationProgramPaguPerOPD.vue'),
  },
  // admin - realitation - program
  {
    path: '/admin/report/formulire78pertahun',
    name: 'ReportFormulirE78PerTahun',
    meta: {
      title: 'REPORT - FORMULIR E-78 PER TAHUN',
    },
    component: () => import('@/pages/admin/report/ReportFormulirE78PerTahun.vue'),
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