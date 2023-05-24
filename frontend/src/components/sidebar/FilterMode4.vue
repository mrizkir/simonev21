<template>
	<v-list-item>
		<v-list-item-content>
			<v-select
				v-model="tahun_anggaran"
				:items="daftar_ta"
				label="TAHUN ANGGARAN"
				outlined
			/>
			<v-select
				v-model="tw_realisasi"
				:items="daftar_tw"
				label="TW REALISASI"
				outlined
			/>
			<v-select
				v-model="tw_rumus"
				:items="daftar_tw"
				label="RUMUS TW"
				outlined
				v-if="showrumustw"
			/>
		</v-list-item-content>
	</v-list-item>
</template>
<script>
	export default {
		name: "FilterMode4",
		created() {
			this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
			this.tahun_anggaran = this.$store.getters["uifront/getTahunAnggaran"];
			this.daftar_tw = this.$store.getters["uifront/getDaftarTW"];
			this.tw_realisasi = this.$store.getters["uifront/getTWRealisasi"];
			this.tw_rumus = this.$store.getters["uifront/getTWRumus"];
		},
		props: {
      showrumustw: {
        type: Boolean,
        default: false,
      },
    },
		data: () => ({
			firstloading: true,
			daftar_tw: [],
			tw_realisasi: null,
			tw_rumus: null,

			daftar_ta: [],
			tahun_anggaran: null,
		}),
		methods: {
			setFirstTimeLoading(bool) {
				this.firstloading = bool;
			},
		},
		watch: {
			tahun_anggaran(val) {
				if (!this.firstloading) {
					this.$store.dispatch("uifront/updateTahunAnggaran", val);
					this.$emit("changeTahunAnggaran", val);
				}
			},
			tw_realisasi(val) {
				if (!this.firstloading) {
					this.$store.dispatch("uifront/updateTWRealisasi", val);
					this.$emit("changeTWRealisasi", val);
				}
			},
			tw_rumus(val) {
				if (!this.firstloading) {
					this.$store.dispatch("uifront/updateTWRumus", val);
					this.$emit("changeTWRumus", val);
				}
			},
		},
	};
</script>
