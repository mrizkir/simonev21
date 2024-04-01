<template>
	<v-list-item>
		<v-list-item-content>
			<v-select
				v-model="tw_realisasi"
				:items="daftar_tw"
				label="TW REALISASI"
				outlined
			/>
		</v-list-item-content>
	</v-list-item>
</template>
<script>
	export default {
		name: "FilterMode4",
		created() {
			this.daftar_tw = this.$store.getters["uifront/getDaftarTW"];
			this.tw_realisasi = this.$store.getters["uifront/getTWRealisasi"];
		},
		data: () => ({
			firstloading: true,
			daftar_tw: [],
			tw_realisasi: null,
		}),
		methods: {
			setFirstTimeLoading(bool) {
				this.firstloading = bool;
			},
		},
		watch: {
			tw_realisasi(val) {
				if (!this.firstloading) {
					this.$store.dispatch("uifront/updateTWRealisasi", val);
					this.$emit("changeTWRealisasi", val);
				}
			},
		},
	};
</script>
