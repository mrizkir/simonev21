import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import vuetify from "./plugins/vuetify";
import api from "./plugins/api";
import "@/plugins/Dayjs";

Vue.use(api);

Vue.config.productionTip = false;

Vue.filter("formatUang", function(value) 
{
	var num = new Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1.");
	var pos = num.lastIndexOf(".");
	num = num.substring(0,pos) + "," + num.substring(pos+1)	
	return num;
});

new Vue({
	router,
	store,
	vuetify,
	render: h => h(App),
}).$mount("#app");
