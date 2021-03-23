import Vue from "vue";
import store from "../store/index";
import VueRouter from "vue-router";
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
