import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import vuetify from "./plugins/vuetify";
import api from "./plugins/api";
import "@/plugins/Dayjs";
import "./scss/main.scss";

Vue.use(api);

Vue.config.productionTip = false;

Vue.filter("formatUang", function(value) {
	var num = new Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1.");
	var pos = num.lastIndexOf(".");
	num = num.substring(0, pos) + "," + num.substring(pos + 1);
	return num;
});
Vue.filter("makeLookPrecision", function(value) {
	if (value) {
		return new Number(value).toFixed(2);
	} else {
		return "0.00";
	}
});
//mixin
Vue.mixin({
	methods: {
		formatKodeWarna(triwulan, value) {
			var style;
			switch (triwulan) {
				case 1 :
					if (value >= 23) {
						style = "sangattinggi";
					} else if (value >= 20 && value < 23) {
						style = "tinggi";
					} else if (value >= 17 && value < 20 ) {
						style = "sedang";
					} else if (value >= 13 && value < 17 ) {
						style = "rendah";
					} else {
						style = "sangatrendah";
					}
				break;
				case 2 :
					if (value >= 45) {
						style = "sangattinggi";
					} else if (value >= 39 && value < 45 ){
						style = "tinggi";
					} else if (value >= 33 && value < 39 ){
						style = "sedang";
					} else if (value >= 26 && value < 33 ){
						style = "rendah";
					} else {
						style = "sangatrendah";
					}
				break;
				case 3 :
					if (value >= 79) {
						style = "sangattinggi";
					} else if (value >= 70 && value < 79 ){
						style = "tinggi";
					} else if (value >= 61 && value < 70 ){
						style = "sedang";
					} else if (value >= 51 && value < 60 ){
						style = "rendah";
					} else {
						style = "sangatrendah";
					}
				break;
				case 4 :
					if (value >= 98) {
						style = "sangattinggi";
					} else if (value >= 95 && value < 98 ){
						style = "tinggi";
					} else if (value >= 92 && value < 95 ){
						style = "sedang";
					} else if (value >= 88 && value < 92 ){
						style = "rendah";
					} else {
						style = "sangatrendah";
					}
				break;
				default :
					if (value >= 91 ) {
						style = "sangattinggi";
					} else if (value >= 76 && value < 91 ){
						style = "tinggi";
					} else if (value >= 66 && value < 76 ){
						style = "sedang";
					} else if (value >= 51 && value < 66 ){
						style = "rendah";
					} else {
						style = "sangatrendah";
					}
			}
			return style;
		},
		formatStyleIndikatorKinerja(realisasi_fisik, realisasi_keuangan) {
			return (realisasi_fisik <= realisasi_keuangan) ? "#E53935" : "#8BC34A";
		}		
	}
})
new Vue({
	router,
	store,
	vuetify,
	render: h => h(App),
}).$mount("#app");
