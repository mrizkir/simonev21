<template>
	<div>
		<v-system-bar
			app
			dark
			:class="this.$store.getters['uifront/getTheme']('V-SYSTEM-BAR-CSS-CLASS')"
		>
			<v-spacer></v-spacer>
			<strong>Hak Akses Sebagai:</strong> {{ ROLE }} |
			<strong>Tahun Anggaran:</strong>
			{{ this.$store.getters["uifront/getTahunAnggaran"] }} |
			<strong>Bulan Realisasi:</strong>
			{{
				$store.getters["uifront/getNamaBulan"](
					$store.getters["uifront/getBulanRealisasi"]
				)
			}}
		</v-system-bar>
	</div>
</template>
<script>
	import { mapGetters } from "vuex";
	export default {
		name: "Dashboard",
		created() {
			this.TOKEN = this.$route.params.token;
			this.tahun_pendaftaran = this.$store.getters["uifront/getTahunAnggaran"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.TOKEN,
				},
				{
					text: "DASHBOARD",
					disabled: true,
					href: "#",
				},
			];
			this.initialize();
		},
		data: () => ({
			breadcrumbs: [],
			TOKEN: null,
			dashboard: null,
			tahun_pendaftaran: "",
		}),
		methods: {
			initialize: async function() {
				await this.$ajax
					.get("/auth/me", {
						headers: {
							Authorization: "Bearer " + this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.dashboard = data.role[0];
						this.$store.dispatch("uiadmin/changeDashboard", this.dashboard);
					})
					.catch(error => {
						if (error.response.status == 401) {
							this.$router.push("/login");
						}
					});
				this.$store.dispatch("uiadmin/init", this.$ajax);
			},
		},
		computed: {
			...mapGetters("auth", {
				ROLE: "Role",
			}),
		},
	};
</script>
