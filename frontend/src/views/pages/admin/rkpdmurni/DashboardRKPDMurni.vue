<template>
  <RKPDMurniLayout :showrightsidebar="false" :temporaryleftsidebar="true">
    <ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				RKPD MURNI
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
					Rencana Kerja Pemerintah Daerah (RKPD) anggaran Murni
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						item-key="FormBMurniID"
						dense
						:single-expand="true"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
						:disable-pagination="true"
						:hide-default-footer="true"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">				
								<v-spacer></v-spacer>
								<v-btn
									color="primary"
									fab
									small
									@click.stop="printtoexcel"
									:disabled="btnLoading"
								>
									<v-icon>mdi-printer</v-icon>
								</v-btn>
							</v-toolbar>
						</template>
						<template v-slot:body="{ items }">
							<tbody>
								<tr
									v-for="item in items"
									v-bind:key="item.RKPDID"
									:class="[colorRowFormA(item), fontWeight(item)]"
								>
									<td>{{ item.kode }}</td>
									<td>{{ item.nama }}</td>									
									<td class="text-right">{{ item.target_renstra | formatUang }}</td>									
								</tr>
							</tbody>
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
  </RKPDMurniLayout>
</template>
<script>
  import RKPDMurniLayout from "@/views/layouts/RKPDMurniLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "DashboardRKPDMurni",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "RKPD MURNI",
					disabled: true,
					href: "#",
				},
			];
			this.tahun_anggaran = new Number(this.$store.getters["auth/TahunSelected"]);				
			this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
		},
		mounted() {
			this.initialize();
		},
		data() {
			var tahun_renja_lalu = new Number(this.$store.getters["auth/TahunSelected"]) - 1;		
			var tahun_renstra_lalu = new Number(this.$store.getters["auth/TahunSelected"]) - 5;
			return {
				btnLoading: false,
				tahun_anggaran: null,
				tahun_renstra_lalu: tahun_renstra_lalu,
				tahun_renja_lalu: tahun_renja_lalu,
				bulan_realisasi: null,
				//data table
				datatable: [],
				datatableLoading: false,
				headers: [
					{ text: "KODE", value: "kode", width: 80, sortable: false },
					{
						text: "PROGRAM/KEGIATAN/SUB KEGIATAN",
						value: "nama_uraian",
						width: 300,
						sortable: false,
					},
					{
						text: "TARGET RENSTRA",
						value: "target_renstra",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "BOBOT (%)",
						value: "bobot1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "TARGET FISIK (%)",
						value: "fisik_target1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "REALISASI FISIK (%)",
						value: "fisik_realisasi1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "TTB FISIK (%)",
						value: "fisik_ttb1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "TARGET KEUANGAN (RP)",
						value: "keuangan_target1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "(%)",
						value: "keuangan_target_persen_1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "REALISASI KEUANGAN (RP)",
						value: "keuangan_realisasi1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "(%)",
						value: "keuangan_realisasi_persen_1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "TTB KEUANGAN (%)",
						value: "keuangan_ttb1",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "LOKASI",
						value: "lokasi",
						align: "left",
						width: 100,
						sortable: false,
					},
					{
						text: "SISA ANGGARAN (RP)",
						value: "sisa_anggaran",
						align: "end",
						width: 100,
						sortable: false,
					},
					{
						text: "(%)",
						value: "sisa_anggaran_persen",
						align: "end",
						width: 100,
						sortable: false,
					},
				],
			};
		},
		methods: {
			async initialize() {
				await this.$ajax
					.post(
						"/rkpdmurni",
						{
							tahun: this.tahun_anggaran,
							no_bulan: this.bulan_realisasi,			
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.rkpd;
					});
			},
			colorRowFormA(item) {
				var color = "";
				if (item.level == 1) {
					color = "lime lighten-1";
				} else if (item.level == 2) {
					color = "light-green lighten-2";
				} else if (item.level == 3) {
					color = "light-green accent-1";
				} else if (item.level == 4) {
					color = "lime lighten-3";
				} else if (item.level == 5) {
					color = "lime lighten-4";
				} else if (item.level == 10) {
					color = "red lighten-4";
				} else {
					color = "white";
				}
				return color;
			},
			fontWeight(item) {
				var weight = "";
				if (item.isprogram == 1) {
					weight = "font-weight-bold";
				} else if (item.level == 2) {
					weight = "font-weight-medium";
				} else if (item.level == 3) {
					weight = "Normal weight text";
				} else {
					weight = "Normal weight text";
				}
				return weight;
			},
		},
    components: {
			RKPDMurniLayout,
      ModuleHeader,
		},
  }
</script>
