import axios from "axios";

export default {
	install(app, options) {
		let ajax = axios.create({
			baseURL: import.meta.env.VITE_APP_API_V1,
		});
		app.config.globalProperties.$api = {
			url: import.meta.env.VITE_APP_HOST,
			storageURL: import.meta.env.VITE_APP_STORAGE_URL,
			post: async function(path) {
				return await ajax.post(path);
			},
		};
		app.config.globalProperties.$ajax = ajax;
	},
};
