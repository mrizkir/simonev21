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
        case 1:
          if (value >= 38.77) {
            style = "sangattinggi";
          } else if (value >= 31.27 && value <= 38.76) {
            style = "tinggi";
          } else if (value >= 18 && value <= 31.26) {
            style = "sedang";
          } else if (value >= 13.01 && value < 17.99) {
            style = "rendah";
          } else {
            style = "sangatrendah";
          }
          break;
        case 2:
          if (value >= 47.51) {
            style = "sangattinggi";
          } else if (value >= 38.76 && value <= 47.5) {
            style = "tinggi";
          } else if (value >= 31.26 && value <= 38.76) {
            style = "sedang";
          } else if (value >= 22.51 && value <= 31.25) {
            style = "rendah";
          } else {
            style = "sangatrendah";
          }
          break;
        case 3:
          if (value >= 69) {
            style = "sangattinggi";
          } else if (value >= 57 && value <= 68) {
            style = "tinggi";
          } else if (value >= 50 && value <= 56) {
            style = "sedang";
          } else if (value >= 38 && value <= 49) {
            style = "rendah";
          } else {
            style = "sangatrendah";
          }
          break;
        case 4:
          if (value >= 91) {
            style = "sangattinggi";
          } else if (value >= 76 && value <= 90) {
            style = "tinggi";
          } else if (value >= 66 && value <= 75) {
            style = "sedang";
          } else if (value >= 51 && value <= 65) {
            style = "rendah";
          } else {
            style = "sangatrendah";
          }
          break;
        default:
          if (value >= 91) {
            style = "sangattinggi";
          } else if (value >= 76 && value < 91) {
            style = "tinggi";
          } else if (value >= 66 && value < 76) {
            style = "sedang";
          } else if (value >= 51 && value < 66) {
            style = "rendah";
          } else {
            style = "sangatrendah";
          }
      }
      return style;
    },
    formatStyleIndikatorKinerja(realisasi_fisik, realisasi_keuangan) {
      return realisasi_fisik <= realisasi_keuangan ? "#E53935" : "#8BC34A";
    },
  },
});
new Vue({
  router,
  store,
  vuetify,
  render: h => h(App),
}).$mount("#app");
