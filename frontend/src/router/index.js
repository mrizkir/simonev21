import Vue from "vue";
import store from "../store/index";
import VueRouter from "vue-router";
import NotFoundComponent from "../components/NotFoundComponent";
Vue.use(VueRouter);
const routes = [
  {
    path: "/",
    name: "FrontDashboard",
    meta: {
      title: "DASHBOARD SIMONEV",
    },
    component: () => import("../views/pages/front/DashboardFront.vue"),
  },
  {
    path: "/pelaporanopd",
    name: "FrontPelaporanOPD",
    meta: {
      title: "PELAPORAN OPD",
    },
    component: () => import("../views/pages/front/PelaporanOPDFront.vue"),
  },
  {
    path: "/evaluasimurni/realisasita",
    name: "FrontEvaluasiRealisasiMurniTA",
    meta: {
      title: "EVALUASI REALISASI MURNI PER T.A",
    },
    component: () =>
      import("../views/pages/front/FrontEvaluasiRealisasiMurniTA.vue"),
  },
  {
    path: "/evaluasimurni/realisasitw",
    name: "FrontEvaluasiRealisasiMurniTW",
    meta: {
      title: "EVALUASI REALISASI MURNI PER TW",
    },
    component: () =>
      import("../views/pages/front/FrontEvaluasiRealisasiMurniTW.vue"),
  },
  {
    path: "/evaluasiperubahan/realisasita",
    name: "FrontEvaluasiRealisasiPerubahanTA",
    meta: {
      title: "LAPORAN REALISASI PERUBAHAN",
    },
    component: () =>
      import("../views/pages/front/FrontEvaluasiRealisasiPerubahanTA.vue"),
  },
  {
    path: "/evaluasiperubahan/realisasitw",
    name: "FrontEvaluasiRealisasiPerubahanTW",
    meta: {
      title: "EVALUASI REALISASI PERUBAHAN PER TW",
    },
    component: () =>
      import("../views/pages/front/FrontEvaluasiRealisasiPerubahanTW.vue"),
  },
  {
    path: "/login",
    name: "Login",
    meta: {
      title: "LOGIN",
    },
    component: () => import("../views/pages/front/Login.vue"),
  },
  //admin
  //dashboard
  {
    path: "/dashboard/:token",
    name: "DashboardAdmin",
    meta: {
      title: "DASHBOARD",
    },
    component: () => import("../views/pages/admin/DashboardAdmin.vue"),
  },
  {
    path: "/gallerypembangunan1/",
    name: "GalleryPembangunanAdmin",
    meta: {
      title: "GALLERY PEMBANGUNAN",
    },
    component: () => import("../views/pages/admin/GalleryPembangunanAdmin.vue"),
  },
  //dmaster
  {
    path: "/dmaster",
    name: "DMaster",
    meta: {
      title: "DATA MASTER",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/DMaster.vue"),
  },
  //dmaster - urusan
  {
    path: "/dmaster/kodefikasi/urusan",
    name: "KodefikasiUrusan",
    meta: {
      title: "KODEFIKASI - URUSAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KodefikasiUrusan.vue"),
  },
  //dmaster - bidang urusan
  {
    path: "/dmaster/kodefikasi/bidangurusan",
    name: "KodefikasiBidangUrusan",
    meta: {
      title: "KODEFIKASI - BIDANG URUSAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KodefikasiBidangUrusan.vue"),
  },
  //dmaster - program
  {
    path: "/dmaster/kodefikasi/program",
    name: "KodefikasiProgram",
    meta: {
      title: "KODEFIKASI - PROGRAM",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KodefikasiProgram.vue"),
  },
  //dmaster - kegiatan
  {
    path: "/dmaster/kodefikasi/kegiatan",
    name: "KodefikasiKegiatan",
    meta: {
      title: "KODEFIKASI - KEGIATAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KodefikasiKegiatan.vue"),
  },
  //dmaster - sub kegiatan
  {
    path: "/dmaster/kodefikasi/subkegiatan",
    name: "KodefikasiSubKegiatan",
    meta: {
      title: "KODEFIKASI - SUB KEGIATAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KodefikasiSubKegiatan.vue"),
  },
  //dmaster - opd
  {
    path: "/dmaster/organisasi/opd",
    name: "DMasterOPD",
    meta: {
      title: "ORGANISASI PERANGKAT DAERAH",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/OPD.vue"),
  },
  //dmaster - unit kerja
  {
    path: "/dmaster/organisasi/unitkerja",
    name: "DMasterUnitKerja",
    meta: {
      title: "UNIT KERJA",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/UnitKerja.vue"),
  },
  //dmaster - rekening - akun
  {
    path: "/dmaster/kodefikasi/akun",
    name: "DMasterRekeningAkun",
    meta: {
      title: "REKENING - AKUN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/RekeningAkun.vue"),
  },
  {
    path: "/dmaster/kodefikasi/kelompok",
    name: "DMasterRekeningKelompok",
    meta: {
      title: "REKENING - KELOMPOK",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/RekeningKelompok.vue"),
  },
  {
    path: "/dmaster/kodefikasi/jenis",
    name: "DMasterRekeningJenis",
    meta: {
      title: "REKENING - JENIS",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/RekeningJenis.vue"),
  },
  {
    path: "/dmaster/kodefikasi/objek",
    name: "DMasterRekeningObjek",
    meta: {
      title: "REKENING - OBJEK",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/RekeningObjek.vue"),
  },
  {
    path: "/dmaster/kodefikasi/rincianobjek",
    name: "DMasterRekeningRincianObjek",
    meta: {
      title: "REKENING - RINCIAN OBJEK",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/RekeningRincianObjek.vue"),
  },
  {
    path: "/dmaster/kodefikasi/subrincianobjek",
    name: "DMasterRekeningSubRincianObjek",
    meta: {
      title: "REKENING - SUB RINCIAN OBJEK",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/RekeningSubRincianObjek.vue"),
  },
  //dmaster - pegawai
  {
    path: "/dmaster/asn",
    name: "DMasterASN",
    meta: {
      title: "PEGAWAI - ASN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/ASN.vue"),
  },
  {
    path: "/dmaster/pejabat",
    name: "DMasterPejabat",
    meta: {
      title: "PEGAWAI - PEJABAT",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/Pejabat.vue"),
  },
  //dmaster - dan lain-lain
  {
    path: "/dmaster/ta",
    name: "DMasterTA",
    meta: {
      title: "DMASTER - TAHUN ANGGARAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/TA.vue"),
  },
  {
    path: "/dmaster/sumberdana",
    name: "DMasterSumberDana",
    meta: {
      title: "DMASTER - SUMBER DANA",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/SumberDana.vue"),
  },
  {
    path: "/dmaster/jenispelaksanaan",
    name: "DMasterJenisPelaksanaan",
    meta: {
      title: "DMASTER - JENIS PELAKSANAAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/JenisPelaksanaan.vue"),
  },
  {
    path: "/dmaster/jenispembangunan",
    name: "DMasterJenisPembangunan",
    meta: {
      title: "DMASTER - JENIS PEMBANGUNAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/JenisPembangunan.vue"),
  },
  //rpjmd
  {
    path: "/rpjmd",
    name: "DashboardRPJMD",
    meta: {
      title: "RENCANA PEMBANGUNAN JANGKA MENENGAH DAERAH",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/rpjmd/DashboardRPJMD.vue"),
  },
  {
    path: "/rpjmd/pengaturan",
    name: "PengaturanRPJMD",
    meta: {
      title: "PENGATURAN RPJMD",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/rpjmd/PengaturanRPJMD.vue"),
  },

  //rkpd murni
  {
    path: "/rkpdmurni",
    name: "DashboardRKPDMurni",
    meta: {
      title: "RENCANA KERJA PEMERINTAH DAERAH MURNI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/rkpdmurni/DashboardRKPDMurni.vue"),
  },
  {
    path: "/rkpdmurni/indikatorkinerja/program",
    name: "IndikatorKinerjaProgram",
    meta: {
      title: "RKPD - INDIKATOR KINERJA PROGRAM",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/rkpdmurni/IndikatorKinerjaProgramMurni.vue"),
  },
  //renja murni
  {
    path: "/renjamurni",
    name: "RenjaMurni",
    meta: {
      title: "RENCANA KERJA MURNI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/RenjaMurni.vue"),
  },
  //renja murni - data mentah
  {
    path: "/renjamurni/datamentah",
    name: "RenjaMurniDataMentah",
    meta: {
      title: "RENCANA KERJA MURNI - DATA MENTAH",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/DataMentahMurni.vue"),
  },
  //renja murni - snapshot - rka
  {
    path: "/renjamurni/snapshot/rka",
    name: "RenjaMurniSnapshotRKA",
    meta: {
      title: "RENCANA KERJA MURNI - SNAPSHOT",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/SnapshotRKAMurni.vue"),
  },
  {
    path: "/renjamurni/snapshot/rka/uraian/:rkaid",
    name: "RenjaMurniSnapshotUraianRKA",
    meta: {
      title: "RKA MURNI - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/SnapshotUraianRKAMurni.vue"),
  },
  {
    path: "/renjamurni/snapshot/rka/realisasi/:rkarincid",
    name: "RenjaMurniSnapshotRealisasiRKAMurni",
    meta: {
      title: "RKA MURNI - REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/SnapshotRealisasiRKAMurni.vue"),
  },
  //renja murni - progres sp2d
  {
    path: "/renjamurni/progressp2d",
    name: "RenjaMurniProgresSp2d",
    meta: {
      title: "RENCANA KERJA MURNI - PROGRESS SP2D",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/ProgresSp2dMurni.vue"),
  },
  //renja murni - rka murni
  {
    path: "/renjamurni/rka",
    name: "RenjaMurniRKA",
    meta: {
      title: "RENCANA KERJA DAN ANGGARAN MURNI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/RKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/uraian/:rkaid",
    name: "RenjaMurniUraianRKA",
    meta: {
      title: "RKA MURNI - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/UraianRKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/:rkaid/edit",
    name: "BelanjaMurniEditRKA",
    meta: {
      title: "RKA MURNI - UBAH",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/EditRKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/uraian/:rkaid",
    name: "BelanjaMurniUraianRKA",
    meta: {
      title: "RKA MURNI - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/UraianRKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/uraian/:rkaid/add",
    name: "RenjaMurniAddUraianRKA",
    meta: {
      title: "RKA MURNI - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/AddUraianRKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/uraian/:rkarincid/edit",
    name: "BelanjaEditUraianRKAMurni",
    meta: {
      title: "RKA MURNI - UBAH URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/EditUraianRKAMurni.vue"),
  },
  {
    path: "/renjamurni/rka/realisasi/:rkarincid",
    name: "BelanjaMurniRealisasiRKAMurni",
    meta: {
      title: "RKA MURNI - REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/RealisasiRKAMurni.vue"),
  },
  {
    path: "/renjamurni/fotorealisasi/:rkarincid",
    name: "RenjaMurniFotoRealisasi",
    meta: {
      title: "RKA MURNI - FOTO REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/FotoRealisasiMurni.vue"),
  },
  //renja murni - pelaporan OPD
  {
    path: "/renjamurni/pelaporanopd",
    name: "RenjaMurniPelaporanOPD",
    meta: {
      title: "BELANJA MURNI - PELAPORAN OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/PelaporanOPDMurni.vue"),
  },
  //renja murni - report form a
  {
    path: "/renjamurni/report/forma",
    name: "ReportFormAMurni",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM A",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/FormAMurni.vue"),
  },
  {
    path: "/renjamurni/report/forma/:rkaid",
    name: "ReportFormAMurniDetail",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM A",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/FormAMurni.vue"),
  },
  {
    path: "/renjamurni/report/formbopd",
    name: "ReportFormBOPDMurni",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM B OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/FormBOPDMurni.vue"),
  },
  {
    path: "/renjamurni/report/formbopd/chart",
    name: "ReportFormBOPDChartMurni",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM B OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/FormBOPDChartMurni.vue"),
  },
  {
    path: "/renjamurni/report/formbunitkerja",
    name: "ReportFormBUnitkerjaMurni",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM B UNIT KERJA",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/FormBUnitKerjaMurni.vue"),
  },
  {
    path: "/renjamurni/report/formbunitkerja/chart",
    name: "ReportFormBUnitkerjaChartMurni",
    meta: {
      title: "BELANJA MURNI - LAPORAN FORM B UNIT KERJA",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/FormBUnitKerjaChartMurni.vue"),
  },
  //renja murni - lra opd
  {
    path: "/renjamurni/report/lraopd",
    name: "FormLRAOPDMurni",
    meta: {
      title: "LAPORAN REALISASI ANGGARAN MURNI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/FormLRAOPDMurni.vue"),
  },
  //renja murni - rekap lra belanja
  {
    path: "/renjamurni/report/rekaplra",
    name: "FormRekapLRAMurni",
    meta: {
      title: "LAPORAN REKAPITULASI LRA BELANJA MURNI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/renjamurni/FormRekapLRAMurni.vue"),
  },
  //renja murni - target kinerja murni
  {
    path: "/renjamurni/targetkinerja",
    name: "RenjaMurniTargetKinerja",
    meta: {
      title: "TARGET KINERJA MURNI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/TargetKinerjaMurni.vue"),
  },
  {
    path: "/renjamurni/targetkinerja/:rkaid",
    name: "RenjaMurniTargetKinerjaDetail",
    meta: {
      title: "TARGET KINERJA MURNI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/TargetKinerjaMurniDetail.vue"),
  },
  // statistik murni
  {
    path: "/renjamurni/statistik/peringkatopd",
    name: "RenjaMurniPeringkatOPD",
    meta: {
      title: "BELANJA MURNI - STATISTIK PERINGKAT OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjamurni/StatistikPeringkatOPDMurni.vue"),
  },
  {
    path: "/renjamurni/statistik/capaianrek",
    name: "RenjaMurniCapaianRekening",
    meta: {
      title: "BELANJA MURNI - STATISTIK CAPAIAN PER REKENING",
      requiresAuth: true,
    },
    component: () =>
      import(
        "../views/pages/admin/renjamurni/StatistikCapaianRekeningMurni.vue"
      ),
  },
  //renja perubahan
  {
    path: "/renjaperubahan",
    name: "RenjaPerubahan",
    meta: {
      title: "RENCANA KERJA PERUBAHAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/RenjaPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/datamentah",
    name: "RenjaPerubahanDataMentah",
    meta: {
      title: "RENCANA KERJA PERUBAHAN - DATA MENTAH",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/DataMentahPerubahan.vue"),
  },
  //renja perubahan - rka perubahan
  {
    path: "/renjaperubahan/rka",
    name: "RenjaPerubahanRKA",
    meta: {
      title: "RENCANA KERJA DAN ANGGARAN PERUBAHAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/RKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/uraian/:rkaid",
    name: "RenjaPerubahanUraianRKA",
    meta: {
      title: "RKA PERUBAHAN - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/UraianRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/:rkaid/edit",
    name: "BelanjaPerubahanEditRKA",
    meta: {
      title: "RKA PERUBAHAN - UBAH",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/EditRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/uraian/:rkaid",
    name: "BelanjaPerubahanUraianRKA",
    meta: {
      title: "RKA PERUBAHAN - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/UraianRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/uraian/:rkaid/add",
    name: "RenjaPerubahanAddUraianRKA",
    meta: {
      title: "RKA PERUBAHAN - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/AddUraianRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/uraian/:rkarincid/edit",
    name: "BelanjaEditUraianRKAPerubahan",
    meta: {
      title: "RKA PERUBAHAN - UBAH URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/EditUraianRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/rka/realisasi/:rkarincid",
    name: "BelanjaPerubahanRealisasiRKAPerubahan",
    meta: {
      title: "RKA PERUBAHAN - REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/RealisasiRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/fotorealisasi/:rkarincid",
    name: "RenjaPerubahanFotoRealisasi",
    meta: {
      title: "RKA PERUBAHAN - FOTO REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FotoRealisasiPerubahan.vue"),
  },
  //renja perubahan - report form a
  {
    path: "/renjaperubahan/report/forma",
    name: "ReportFormAPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM A",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/report/forma/:rkaid",
    name: "ReportFormAPerubahanDetail",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM A",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/report/formbopd",
    name: "ReportFormBOPDPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM B OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormBOPDPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/report/formbopd/chart",
    name: "ReportFormBOPDChartPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM B OPD",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormBOPDChartPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/report/formbunitkerja",
    name: "ReportFormBUnitKerjaPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM B UNIT KERJA",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormBUnitKerjaPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/report/formbunitkerja/chart",
    name: "ReportFormBUnitkerjaChartPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - LAPORAN FORM B UNIT KERJA",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/FormBUnitKerjaChartPerubahan.vue"),
  },
  //renja perubahan - target kinerja perubahan
  {
    path: "/renjaperubahan/targetkinerja",
    name: "RenjaPerubahanTargetKinerja",
    meta: {
      title: "TARGET KINERJA PERUBAHAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/TargetKinerjaPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/targetkinerja/:rkaid",
    name: "RenjaPerubahanTargetKinerjaDetail",
    meta: {
      title: "TARGET KINERJA PERUBAHAN",
      requiresAuth: true,
    },
    component: () =>
      import(
        "../views/pages/admin/renjaperubahan/TargetKinerjaPerubahanDetail.vue"
      ),
  },
  //renja perubahan - snapshot - rka
  {
    path: "/renjaperubahan/snapshot/rka",
    name: "RenjaPerubahanSnapshotRKA",
    meta: {
      title: "RENCANA KERJA MURNI - SNAPSHOT",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/renjaperubahan/SnapshotRKAPerubahan.vue"),
  },
  {
    path: "/renjaperubahan/snapshot/rka/uraian/:rkaid",
    name: "RenjaPerubahanSnapshotUraianRKA",
    meta: {
      title: "RKA MURNI - URAIAN",
      requiresAuth: true,
    },
    component: () =>
      import(
        "../views/pages/admin/renjaperubahan/SnapshotUraianRKAPerubahan.vue"
      ),
  },
  {
    path: "/renjaperubahan/snapshot/rka/realisasi/:rkarincid",
    name: "RenjaPerubahanSnapshotRealisasiRKAPerubahan",
    meta: {
      title: "RKA MURNI - REALISASI",
      requiresAuth: true,
    },
    component: () =>
      import(
        "../views/pages/admin/renjaperubahan/SnapshotRealisasiRKAPerubahan.vue"
      ),
  },
  //dak murni
  {
    path: "/dakmurni",
    name: "DAKMurni",
    meta: {
      title: "DAK MURNI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dakmurni/DAKMurni.vue"),
  },
  // statistik perubahan
  {
    path: "/renjaperubahan/statistik/peringkatopd",
    name: "RenjaMurniPeringkatOPDPerubahan",
    meta: {
      title: "BELANJA PERUBAHAN - STATISTIK PERINGKAT OPD",
      requiresAuth: true,
    },
    component: () =>
      import(
        "../views/pages/admin/renjaperubahan/StatistikPeringkatOPDPerubahan.vue"
      ),
  },
  //system
  {
    path: "/system-users",
    name: "SystemUsers",
    meta: {
      title: "SYSTEM - USERS",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SystemUsers.vue"),
  },
  {
    path: "/system-users/permissions",
    name: "UsersPermissions",
    meta: {
      title: "USERS - PERMISSIONS",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Permissions.vue"),
  },
  {
    path: "/system-users/roles",
    name: "UsersRoles",
    meta: {
      title: "USERS - ROLES",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Roles.vue"),
  },
  {
    path: "/system-users/superadmin",
    name: "UsersSuperadmin",
    meta: {
      title: "USERS - SUPER ADMIN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersSuperadmin.vue"),
  },
  {
    path: "/system-users/bapelitbang",
    name: "UsersBapelitbang",
    meta: {
      title: "USERS - BAPPELITBANG",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersBapelitbang.vue"),
  },
  {
    path: "/system-users/opd",
    name: "UsersOPD",
    meta: {
      title: "USERS - OPD",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersOPD.vue"),
  },
  {
    path: "/system-users/unitkerja",
    name: "UsersUnitKerja",
    meta: {
      title: "USERS - UNIT KERJA",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersUnitKerja.vue"),
  },
  {
    path: "/system-users/dewan",
    name: "UsersDewan",
    meta: {
      title: "USERS - DEWAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersDewan.vue"),
  },
  {
    path: "/system-users/tapd",
    name: "UsersTAPD",
    meta: {
      title: "USERS - TAPD",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersTAPD.vue"),
  },
  {
    path: "/system-users/profil",
    name: "UsersProfil",
    meta: {
      title: "USERS - PROFILE",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersProfile.vue"),
  },
  //system
  {
    path: "/system-setting",
    name: "SystemSetting",
    meta: {
      title: "SETTING - SISTEM",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SystemSetting.vue"),
  },
  {
    path: "/system-setting/variables",
    name: "Variables",
    meta: {
      title: "SETTING - VARIABLES",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Variables.vue"),
  },
  {
    path: "/system-setting/lockopd",
    name: "LockOPD",
    meta: {
      title: "SETTING - LOCK OPD",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/LockOPD.vue"),
  },
  // mobile - chart
  {
    path: "/mobile/chartmurni/keuangan/:ta/:bulan",
    name: "MobileChartMurniKeuangan",
    meta: {
      title: "REALISASI KEUANGAN MURNI",
    },
    component: () =>
      import("../views/pages/mobile/MobileChartMurniKeuangan.vue"),
  },
  {
    path: "/mobile/chartmurni/fisik/:ta/:bulan",
    name: "MobileChartMurniFisik",
    meta: {
      title: "REALISASI FISIK MURNI",
    },
    component: () => import("../views/pages/mobile/MobileChartMurniFisik.vue"),
  },
  {
    path: "/mobile/chartperubahan/keuangan/:ta/:bulan",
    name: "MobileChartPerubahanKeuangan",
    meta: {
      title: "REALISASI KEUANGAN PERUBAHAN",
    },
    component: () =>
      import("../views/pages/mobile/MobileChartPerubahanKeuangan.vue"),
  },
  {
    path: "/mobile/chartperubahan/fisik/:ta/:bulan",
    name: "MobileChartPerubahanFisik",
    meta: {
      title: "REALISASI FISIK PERUBAHAN",
    },
    component: () =>
      import("../views/pages/mobile/MobileChartPerubahanFisik.vue"),
  },
  // other page
  {
    path: "/404",
    name: "NotFoundComponent",
    meta: {
      title: "PAGE NOT FOUND",
    },
    component: NotFoundComponent,
  },
  {
    path: "*",
    redirect: "/404",
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});
router.beforeEach((to, from, next) => {
  document.title = to.meta.title;
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (store.getters["auth/Authenticated"]) {
      next();
      return;
    }
    next("/login");
  } else {
    next();
  }
});
export default router;
