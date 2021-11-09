<template>
  <FrontLayout>
    <template v-slot:system-bar>
			Tahun Anggaran: {{ tahun_anggaran }} | Bulan Realisasi:
			{{
				$store.getters["uifront/getNamaBulan"](
					$store.getters["uifront/getBulanRealisasi"]
				)
			}}
		</template>
    <ModuleHeader>
			<template v-slot:icon>
				mdi-chart-bar
			</template>
			<template v-slot:name>
				LAPORAN REALISASI MURNI
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="cyan" border="left" colored-border type="info">
					Laporan realisasi per OPD Murni dengan posisi bulan dan tahun anggaran terakhir.
				</v-alert>
			</template>
		</ModuleHeader>
    <v-container fluid>
      <v-row dense class="mb-2">
        <v-col xs="12" sm="6" md="3">
          <v-card color="#b71b1c" dark>
						<v-card-title class="headline">
							SANGAT RENDAH
						</v-card-title>
						<v-card-subtitle>
							Triwulan & Tahun
						</v-card-subtitle>
						<v-card-text>
							Triwulan I (x &lt; 13%)<br />Triwulan II (x &lt;= 25%) <br />Triwulan III (x &lt;= 50%) <br />Triwulan IV (x &lt;= 88%)<br />Tahun (x &lt;=50%)
						</v-card-text>
					</v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#d49dc5" dark>
						<v-card-title class="headline">
							RENDAH
						</v-card-title>
						<v-card-subtitle>
							Triwulan & Tahun
						</v-card-subtitle>
						<v-card-text>
							Triwulan I (13% >= x &lt; 17%)<br />Triwulan II (26% >= x &lt; 33%) <br />Triwulan III (51% >= x &lt; 60%) <br />Triwulan IV (88% >= x &lt; 92%)<br />Tahun (51% >= x &lt; 66%)
						</v-card-text>
					</v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#e1c027" dark>
						<v-card-title class="headline">
							SEDANG
						</v-card-title>
						<v-card-subtitle>
							Triwulan & Tahun
						</v-card-subtitle>
						<v-card-text>
							Triwulan I (17% >= x &lt; 20%)<br />Triwulan II (33% >= x &lt; 39%) <br />Triwulan III (61% >= x &lt; 70%) <br />Triwulan IV (92% >= x &lt; 95%)<br />Tahun (66% >= x &lt; 76%)
						</v-card-text>
					</v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="2">
          <v-card color="#8ec189" dark>
						<v-card-title class="headline">
							TINGGI
						</v-card-title>
						<v-card-subtitle>
							Triwulan & Tahun
						</v-card-subtitle>
						<v-card-text>
							Triwulan I (20% >= x &lt; 23%)<br />Triwulan II (39% >= x &lt; 45%) <br />Triwulan III (70% >= x &lt; 79%) <br />Triwulan IV (95% >= x &lt; 98%)<br />Tahun (76% >= x &lt; 91%)
						</v-card-text>
					</v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="6" md="3">
          <v-card color="#29af28" dark>
						<v-card-title class="headline">
							SANGAT TINGGI
						</v-card-title>
						<v-card-subtitle>
							Triwulan & Tahun
						</v-card-subtitle>
						<v-card-text>
							Triwulan I (x >= 23%)<br />Triwulan II (x >= 45%) <br />Triwulan III (x >= 79%) <br />Triwulan IV (x >= 98%)<br />Tahun (x >= 91%)
						</v-card-text>
					</v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
      <v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						item-key="kode_organisasi"						
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
						class="elevation-1"
						:disable-pagination="true"
						:hide-default-footer="true"
						dense
					>
            <template v-slot:item.indikator_kinerja="{ item }">							
              <v-chip                
                :color="item.indikator_kinerja"
                class="ma-2"                
              >
                SANGAT RENDAH
              </v-chip>
						</template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </FrontLayout>
</template>
<script>
  import FrontLayout from "@/views/layouts/FrontLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "FrontLaporanRealisasiMurni",
    created() {
			this.$store.dispatch("uifront/init", this.$ajax);
      this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/",
				},				
				{
					text: "LAPORAN REALISASI MURNI",
					disabled: true,
					href: "#",
				},
			];
			this.tahun_anggaran = this.$store.getters["uifront/getTahunAnggaran"];
			this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];

      this.initialize();
		},
    data: () => ({
      firstloading: true,
      breadcrumbs: [],
			tahun_anggaran: null,

      datatableLoading: false,

      //data table
      datatable: [],
      headers: [
        { text: "NO", value: "index", width: 100 },
				{ text: "KODE", value: "kode_organisasi", width: 160, sortable: false },
				{ text: "NAMA OPD", value: "Nm_Organisasi", sortable: false },
				{ text: "TARGET FISIK", value: "target_fisik", sortable: false },
				{ text: "REALISASI FISIK", value: "realisasi_fisik", sortable: false },
				{ text: "TARGET KEUANGAN", value: "target_keuangan", sortable: false },
				{ text: "REALISASI KEUANGAN", value: "realisasi_keuangan", sortable: false },
				{ text: "INDIKATOR KINERJA", value: "indikator_kinerja", sortable: false },
      ],
    }),
    methods: {
      initialize() {
				this.datatableLoading = true;
				this.$ajax
					.post(
						"/dashboard/laporanrealisasimurni",
						{
							tahun: this.tahun_anggaran,							
						},						
					)
					.then(({ data }) => {						
						this.datatableLoading = false;
            this.datatable = data.laporan_realisasi;
					});
			},
    },
    components: {
      FrontLayout,
      ModuleHeader,
    },
  };
</script>
