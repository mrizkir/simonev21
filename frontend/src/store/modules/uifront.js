const getDefaultState = () => {
	return {
		loaded: false,
		daftar_ta: [],
		tahun_anggaran: null,
		daftar_bulan: [],
		bulan_realisasi: null,
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
	setBulanRealisasi(state, bulan) {
		state.bulan_realisasi = bulan;
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
		if (key == "" || key == null || key == "undefined") {
			return "N.A";
		} else {
			var daftar_bulan = state.daftar_bulan.find(el => el.value == key);
			return daftar_bulan.text;
		}
	},
	getBulanRealisasi: state => {
		var bulan = state.bulan_realisasi == null ? 1 : state.bulan_realisasi;
		return bulan;
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
		if (!state.loaded) {
			ajax.get("/system/setting/uifront").then(({ data }) => {
				commit("setDaftarTA", data.daftar_ta);
				commit("setTahunAnggaran", data.tahun_anggaran);
				commit("setDaftarBulan", data.daftar_bulan);
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
	reinit({ commit }) {
		commit("resetState");
	},
};
export default {
	namespaced: true,
	state,
	mutations,
	getters,
	actions,
};
