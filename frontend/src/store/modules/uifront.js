const getDefaultState = () => {
	return {
		loaded: false,
		daftar_ta: [],
		tahun_anggaran: null,
		bulan_realisasi: null,
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
	setBulanRealisasi(state, bulan) {
		state.bulan_realisasi = bulan;
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
		var ta = state.tahun_anggaran == null ? new Date().getFullYear() : state.tahun_anggaran;
		return ta;
	},
	getBulanRealisasi: state => {
		var bulan = state.bulan_realisasi == null ? 1 : state.bulan_realisasi;
		return bulan;
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
			await ajax.get("/system/setting/uifront")
				.then(({ data }) => {
					commit("setDaftarTA", data.daftar_ta);
					commit("setTahunAnggaran", data.tahun_anggaran);
					commit("setBulanRealisasi", data.bulan_realisasi);
					commit("setTheme", data.theme);
					commit("setLoaded", true);
			});
		}
	},
};
export default {
	namespaced: true,
	state,
	mutations,
	getters,
	actions,
};
