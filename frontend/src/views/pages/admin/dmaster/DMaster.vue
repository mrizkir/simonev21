<template>
	<DataMasterLayout :showrightsidebar="false">
		<v-container fluid>
			<v-row dense>
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							JUMLAH URUSAN
						</v-card-title>
						<v-card-subtitle>
							T.A 							
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.jumlah_urusan }}
						</v-card-text>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							JUMLAH PROGRAM
						</v-card-title>
						<v-card-subtitle>
							T.A 							
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.jumlah_program }}
						</v-card-text>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							JUMLAH KEGIATAN
						</v-card-title>
						<v-card-subtitle>
							T.A 							
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.jumlah_kegiatan }}
						</v-card-text>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
				<v-col xs="12" sm="6" md="3">
					<v-card color="#385F73" dark>
						<v-card-title class="headline">
							JUMLAH SUB KEG.
						</v-card-title>
						<v-card-subtitle>
							T.A 							
							{{ $store.getters["auth/TahunSelected"] }}
						</v-card-subtitle>
						<v-card-text>
							{{ statistik1.jumlah_sub_kegiatan }}
						</v-card-text>
					</v-card>
				</v-col>
				<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
			</v-row>
		</v-container>
	</DataMasterLayout>
</template>
<script>
	import DataMasterLayout from "@/views/layouts/DataMasterLayout";
	export default {
		name: "DMaster",
		created() {
			this.tahun_anggaran = this.$store.getters["auth/TahunSelected"];	
			this.initialize();
		},
		data: () => ({
			tahun_anggaran: null,
			statistik1: {
				jumlah_urusan: 0,
				jumlah_program: 0,
      	jumlah_kegiatan: 0,
      	jumlah_sub_kegiatan: 0,
			},
		}),
		methods: {
			async initialize() {
				await this.$ajax
					.post(
						"/dmaster",
						{
							ta: this.$store.getters["auth/TahunSelected"],			
						}
					)
					.then(({ data }) => {
						this.statistik1 = data.dmaster;
					});
			},
		},
		components: {
			DataMasterLayout,
		},
	};
</script>
