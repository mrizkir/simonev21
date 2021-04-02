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
	{
		path: '/dmaster/kodefikasi/kelompokurusan',
		name: 'KodefikasiKelompokUrusan',
		meta:{
			title: "KODEFIKASI - KELOMPOK URUSAN",
			requiresAuth: true,
		},
		component: () => import("../views/pages/admin/dmaster/KodefikasiKelompokUrusan.vue"),		
	},
	// other page
	{
		path: "/404",
		name: "NotFoundComponent",
		meta: {
			title: "PAGE NOT FOUND"
		},
		component: NotFoundComponent
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
