const getDefaultState = () => {
  return {
    loaded: false,
    daftar_ta: [],
    tahun_anggaran: null,
    masa_pelaporan: "murni",
    daftar_bulan: [],
    bulan_realisasi: null,
    daftar_tw: [
      {
        text: 1,
        value: 1,
      },
      {
        text: 2,
        value: 2,
      },
      {
        text: 3,
        value: 3,
      },
      {
        text: 4,
        value: 4,
      },
    ],
    tw_realisasi: null,
    tw_rumus: null,
    identitas: {
      nama_app: "",
      nama_app_alias: "",
      nama_opd: "",
      nama_opd_alias: "",
    },
    theme: null,
  };
};
const state = getDefaultState();
//mutations
const mutations = {
  setLoaded(state, loaded) {
    state.loaded = loaded;
  },
  setDaftarTA(state, daftar) {
    state.daftar_ta = daftar;
  },
  setTahunAnggaran(state, ta) {
    state.tahun_anggaran = ta;
  },
  setDaftarBulan(state, daftar) {
    state.daftar_bulan = daftar;
  },
  setMasaPelaporan(state, masa_pelaporan) {
    state.masa_pelaporan = masa_pelaporan;
  },
  setBulanRealisasi(state, bulan) {
    state.bulan_realisasi = bulan;
  },
  setDaftarTW(state, daftar) {
    state.daftar_tw = daftar;
  },
  setTWRealisasi(state, tw) {
    state.tw_realisasi = tw;
  },
  setTWRumus(state, tw) {
    state.tw_rumus = tw;
  },
  setIdentitas(state, identitas) {
    state.identitas = identitas;
  },
  setTheme(state, theme) {
    state.theme = theme;
  },
  resetState(state) {
    Object.assign(state, getDefaultState());
  },
};
const getters = {
  getDaftarTA: state => {
    return state.daftar_ta;
  },
  getTahunAnggaran: state => {
    var ta =
      state.tahun_anggaran == null
        ? new Date().getFullYear()
        : state.tahun_anggaran;
    return ta;
  },
  getDaftarBulan: state => {
    return state.daftar_bulan;
  },
  getNamaBulan: state => key => {
    if (key == "" || key == null || typeof key == "undefined") {
      return "N.A";
    } else {
      var daftar_bulan = state.daftar_bulan.find(el => el.value == key);
      return typeof daftar_bulan === "undefined" ? "N.A" : daftar_bulan.text;
    }
  },
  getMasaPelaporan: state => {
    return state.masa_pelaporan;
  },
  getBulanRealisasi: state => {
    var bulan = state.bulan_realisasi == null ? 1 : state.bulan_realisasi;
    return bulan;
  },
  getDaftarTW: state => {
    return state.daftar_tw;
  },
  getTWRealisasi: state => {
    var tw_realisasi = state.tw_realisasi == null ? 1 : state.tw_realisasi;
    return tw_realisasi;
  },
  getTWRumus: state => {
    var tw_rumus = state.tw_rumus == null ? 1 : state.tw_rumus;
    return tw_rumus;
  },
  getNamaAPP: state => {
    return state.identitas.nama_app;
  },
  getNamaAPPAlias: state => {
    return state.identitas.nama_app_alias;
  },
  getTheme: state => key => {
    return state.theme == null ? "" : state.theme[key];
  },
};
const actions = {
  init: async function({ commit, state }, ajax) {
    //dipindahkan kesini karena ada beberapa kasus yang melaporkan ini membuat bermasalah.
    commit("setLoaded", false);
    if (!state.loaded && typeof ajax != "undefined") {
      ajax.get("/system/setting/uifront").then(({ data }) => {
        commit("setDaftarTA", data.daftar_ta);
        commit("setTahunAnggaran", data.tahun_anggaran);
        commit("setDaftarBulan", data.daftar_bulan);
        commit("setMasaPelaporan", data.masa_pelaporan);
        commit("setBulanRealisasi", data.bulan_realisasi);      
        commit("setIdentitas", data.identitas);
        commit("setTheme", data.theme);
        commit("setLoaded", true);
      });
    }
  },
  updateTahunAnggaran({ commit }, tahun) {
    commit("setTahunAnggaran", tahun);
  },
  updateBulanRealisasi({ commit }, bulan) {
    commit("setBulanRealisasi", bulan);
  },
  updateTWRealisasi({ commit }, tw) {
    commit("setTWRealisasi", tw);
  },
  updateTWRumus({ commit }, tw) {
    commit("setTWRumus", tw);
  },
  reinit({ commit, dispatch }, ajax) {
    commit("resetState");
    dispatch("init", ajax);
  },
};
export default {
  namespaced: true,
  state,
  mutations,
  getters,
  actions,
};
