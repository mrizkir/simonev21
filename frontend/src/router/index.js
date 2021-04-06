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
