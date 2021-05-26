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
	{
		path: "/dmaster/ta",
		name: "DMasterTA",
		meta: {
			title: "DLL - TAHUN ANGGARAN",
			requiresAuth: true,
		},
		component: () => import("../views/pages/admin/dmaster/TA.vue"),
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
		component: () => import("../views/pages/admin/renjamurni/UraianRKAMurni.vue"),		
	},
	{
		path: "/renjamurni/rka/uraian/:rkarincid/edit",
		name: "BelanjaEditUraianRKAMurni",
		meta: {
			title: "RKA MURNI - UBAH URAIAN",
			requiresAuth: true,
		},
		component: () => import("../views/pages/admin/renjamurni/EditUraianRKAMurni.vue"),		
	},
	{
		path: "/renjamurni/rka/realisasi/:rkarincid",
		name: "BelanjaMurniRealisasiRKAMurni",
		meta: {
			title: "RKA MURNI - REALISASI",
			requiresAuth: true,
		},
		component: () => import("../views/pages/admin/renjamurni/RealisasiRKAMurni.vue"),		
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
