<template>
	<RenjaPerubahanLayout :showrightsidebar="false" :temporaryleftsidebar="true">
		<v-container fluid>
			<v-row dense>
				<v-col cols="12">
					<v-alert type="info">
						Nilai persen realisasi keuangan tetap muncul 0% bila kurang dari
						0.01%
					</v-alert>
				</v-col>
			</v-row>
			<v-row dense>
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							APBD {{ $store.getters["auth/TahunSelected"] }}
							<v-spacer />
							<v-icon large @click.stop="loadstatistik1">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-subtitle>
							Total Pagu APBD Perubahan TA
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.PaguDana2 | formatUang }}
						</v-card-text>
						<v-card-actions>
							<v-progress-linear
								v-model="statistik1.PersenRealisasiKeuangan2"
								color="success"
								background-color="error"
							>
							</v-progress-linear>
						</v-card-actions>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							PROG. DAN KEG.
							<v-spacer />
							<v-icon large @click.stop="loadstatistik1">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-subtitle>
							Jumlah Program dan Sub Kegiatan TA
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							Prog.: {{ statistik1.JumlahProgram2 }} / Sub Keg.:
							{{ statistik1.JumlahKegiatan2 }}
						</v-card-text>
						<v-card-actions>
							<v-progress-linear
								v-model="statistik1.PersenRealisasiKeuangan2"
								color="success"
								background-color="error"
							>
							</v-progress-linear>
						</v-card-actions>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							KEUANGAN
							<v-spacer />
							<v-icon large @click.stop="loadstatistik1">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-subtitle>
							Realisasi Keuangan TA
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.RealisasiKeuangan2 | formatUang }}
							({{ statistik1.PersenRealisasiKeuangan2 }}%)
						</v-card-text>
						<v-card-actions>
							<v-progress-linear
								v-model="statistik1.PersenRealisasiKeuangan2"
								color="success"
								background-color="error"
							>
							</v-progress-linear>
						</v-card-actions>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							FISIK
							<v-spacer />
							<v-icon large @click.stop="loadstatistik1">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-subtitle>
							Realisasi Fisik TA
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>{{ statistik1.RealisasiFisik2 }} %</v-card-text>
						<v-card-actions>
							<v-progress-linear
								v-model="statistik1.RealisasiFisik2"
								color="success"
								background-color="error"
							>
							</v-progress-linear>
						</v-card-actions>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
			</v-row>
			<v-row dense>
				<v-col cols="12">
					<v-card class="mb-2">
						<v-card-title class="headline">
							Progres Realisasi Keuangan
							<v-spacer />
							<v-icon large @click.stop="loadstatistik2">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-text>
							<chart-realisasi-keuangan
								:datagrafik="chartrealisasikeuangan"
								v-if="chartLoaded"
							/>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row dense>
				<v-col cols="12">
					<v-card class="mb-2">
						<v-card-title class="headline">
							Progres Realisasi Fisik
							<v-spacer />
							<v-icon large @click.stop="loadstatistik2">
								mdi-database-refresh
							</v-icon>
						</v-card-title>
						<v-card-text>
							<chart-realisasi-fisik
								:datagrafik="chartrealisasifisik"
								v-if="chartLoaded"
							/>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</RenjaPerubahanLayout>
</template>
<script>
	import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
	import ChartTargetRealisasi from "@/components/chart/ChartTargetRealisasi";
	export default {
		name: "RenjaPerubahan",
		created() {
			this.tahun_anggaran = this.$store.getters["auth/TahunSelected"];
			this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
			this.initialize();
		},
		data: () => ({
			tahun_anggaran: null,
			bulan_realisasi: null,
			statistik1: {
				PaguDana2: 0,
				JumlahProgram2: 0,
				JumlahKegiatan2: 0,
				RealisasiKeuangan2: 0,
				RealisasiFisik2: 0,
			},
			//chart
			chartLoaded: false,
			chartrealisasikeuangan: [[], []],
			chartrealisasifisik: [[], []],
		}),
		methods: {
			async initialize() {
				await this.$ajax
					.post(
						"/renjaperubahan",
						{
							ta: this.tahun_anggaran,
							bulan_realisasi: this.bulan_realisasi,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.statistik1 = data.statistik1;
						//chart realisasi-keuangan
						var realisasi_keuangan = data.chart_keuangan;
						var realisasi_fisik = data.chart_fisik;
						this.chartrealisasikeuangan[0] = realisasi_keuangan[0];
						this.chartrealisasikeuangan[1] = realisasi_keuangan[1];
						this.chartrealisasifisik[0] = realisasi_fisik[0];
						this.chartrealisasifisik[1] = realisasi_fisik[1];
						this.chartLoaded = true;
					});
			},
			async loadstatistik1() {
				await this.$ajax
					.post("/renjaperubahan/reloadstatistik1", {
						ta: this.tahun_anggaran,
					},
					{
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(()=>{                 
						this.$router.go();
					});     
        },
        async loadstatistik2() {
					await this.$ajax
						.post("/renjaperubahan/reloadstatistik2",
            {
                ta: this.tahun_anggaran,
            },
            {
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
            })
						.then(()=>{                 
                this.$router.go();
            });     
        }
		},
		components: {
			RenjaPerubahanLayout,
			"chart-realisasi-keuangan": ChartTargetRealisasi,
			"chart-realisasi-fisik": ChartTargetRealisasi,
		},
	};
</script>
