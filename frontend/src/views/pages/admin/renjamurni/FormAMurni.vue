<template>
	<RenjaMurniLayout :temporaryleftsidebar="true">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				FORM A MURNI
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
					Laporan Form A Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja
					APBD Murni s.d
					<strong>
						BULAN {{ nama_bulan }} T.A
						{{ $store.getters["uifront/getTahunAnggaran"] }}
					</strong>
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid v-if="formadetail">

		</v-container>
		<v-container fluid v-else>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-title>
							FILTER
						</v-card-title>
						<v-card-text>
							<v-autocomplete
								:items="daftar_opd"
								v-model="OrgID_Selected"
								label="OPD / SKPD"
								item-text="Nm_Organisasi"
								item-value="OrgID"
							>
							</v-autocomplete>
							<v-autocomplete
								:items="daftar_unitkerja"
								v-model="SOrgID_Selected"
								label="UNIT KERJA"
								item-text="Nm_Sub_Organisasi"
								item-value="SOrgID"
							>
							</v-autocomplete>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-text>
							<v-text-field
								v-model="search"
								append-icon="mdi-database-search"
								label="Search"
								single-line
								hide-details
							></v-text-field>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-alert type="info">
						Catatan: Nilai realisasi keuangan dan fisik dihitung akumulasi s.d
						<strong>
							BULAN {{ nama_bulan }} T.A
							{{ $store.getters["uifront/getTahunAnggaran"] }}
						</strong>
					</v-alert>
						<v-data-table
						:headers="headers"
						:items="datatable"
						:search="search"
						item-key="FormAMurniID"
						dense
						:single-expand="true"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
						:disable-pagination="true"
						:hide-default-footer="true"
						style="font-size: 11px"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR KEGIATAN</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
							</v-toolbar>
						</template>
						<template v-slot:body="{ items }">
							<tbody>
								<tr
									v-for="item in items"
									v-bind:key="item.FormBMurniID"		
									:class="[colorRowFormA(item),fontWeight(item)]"						
								>	
									<td>{{ item.kode }}</td>
									<td>{{ item.nama_uraian }}</td>
									<td class="text-right">
										{{ item.pagu_dana1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.fisik_target1 }}
									</td>
									<td class="text-right">
										{{ item.fisik_realisasi1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.keuangan_target1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.keuangan_realisasi1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.keuangan_realisasi_persen_1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.sisa_anggaran | formatUang }}
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="orange font-weight-bold dark">
									<td colspan="2" class="text-right">TOTAL</td>
									<td class="text-right">
										{{ total_data.totalPaguUnit | formatUang }}
									</td>
									<td class="text-right">
										{{ total_data.totalPersenTargetFisik | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{
											total_data.totalPersenRealisasiFisik | makeLookPrecision
										}}
									</td>
									<td class="text-right">
										{{ total_data.totalTargetKeuanganKeseluruhan | formatUang }}
									</td>
									<td class="text-right">
										{{
											total_data.totalRealisasiKeuanganKeseluruhan | formatUang
										}}
									</td>
									<td class="text-right">
										{{
											total_data.totalPersenTargetKeuangan | makeLookPrecision
										}}
									</td>
									<td class="text-right">
										{{ total_data.totalSisaAnggaran | formatUang }}
									</td>
								</tr>
							</tfoot>
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
		<template v-slot:filtersidebar>
			<Filter2 v-on:changeBulanRealisasi="changeBulanRealisasi" ref="filter2" />
		</template>
	</RenjaMurniLayout>
</template>
<script>
	import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter2 from "@/components/sidebar/FilterMode2";

	export default {
		name: "FormAMurni",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "RENCANA KERJA MURNI",
					disabled: false,
					href: "/renjamurni",
				},
				{
					text: "LAPORAN",
					disabled: false,
					href: "#",
				},
				{
					text: "FORM A",
					disabled: true,
					href: "#",
				},
			];
			this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
			this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
				this.bulan_realisasi
			);

			this.$store.dispatch("uiadmin/addToPages", {
				name: "formamurni",
				OrgID_Selected: "",
				SOrgID_Selected: "",
				datakegiatan: [],
				formadetail: false,
			});
		},
		mounted() {
			this.formadetail = this.$store.getters["uiadmin/AtributeValueOfPage"](
				"formamurni",
				"formadetail"
			);
			if (this.formadetail) {
				this.firstloading = false;
				this.$refs.filter2.setFirstTimeLoading(this.firstloading);
			} else {
				this.fetchOPD();
				var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
					"formamurni",
					"OrgID_Selected"
				);
				var SOrgID_Selected = this.$store.getters[
					"uiadmin/AtributeValueOfPage"
				]("formamurni", "SOrgID_Selected");
				if (OrgID_Selected.length > 0) {
					this.OrgID_Selected = OrgID_Selected;
					this.SOrgID_Selected = SOrgID_Selected;
				}
				if (SOrgID_Selected.length > 0) {
					this.OrgID_Selected = OrgID_Selected;
					this.SOrgID_Selected = SOrgID_Selected;
					this.firstloading = false;
					this.$refs.filter2.setFirstTimeLoading(this.firstloading);
				}
			}
		},
		data() {
			return {
				firstloading: true,
				bulan_realisasi: null,
				nama_bulan: null,

				//filter form
				daftar_opd: [],
				OrgID_Selected: "",
				daftar_unitkerja: [],
				SOrgID_Selected: "",
				//Organisasi
				DataOPD: null,
				DataUnitKerja: null,

				formadetail: false,

				//data table
				datatableLoaded: true,
				datatableLoading: false,
				datatable: [],
				headers: [
					{
						text: "KODE",
						value: "kode",
						width: 80,
						sortable: false,
					},
					{
						text: "PROGRAM/KEGIATAN/SUB KEGIATAN",
						value: "nama",
						width: 300,
						sortable: false,
					},
					{
						text: "PAGU DANA (RP)",
						value: "pagu_dana1",
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
						text: "TARGET KEUANGAN (RP)",
						value: "keuangan_target1",
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
						text: "%",
						value: "keuangan_realisasi_persen_1",
						align: "end",
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
				],
				search: "",
				total_data: {
					totalPaguUnit: 0,
					totalTargetKeuanganKeseluruhan: 0,
					totalRealisasiKeuanganKeseluruhan: 0,
					totalPersenTargetFisik: 0,
					totalPersenRealisasiFisik: 0,
					totalSisaAnggaran: 0,
				},
				total_forma: {
					totalPaguDana: 0,
					totalPersenBobot: 0,
					totalRealisasiKeuanganKeseluruhan: 0,
					totalPersenTargetFisik: 0,
					totalPersenRealisasiFisik: 0,
					totalPersenTargetKeuangan: 0,
					totalPersenRealisasiKeuangan: 0,
					totalSisaAnggaran: 0,
					totalPersenSisaAnggaran: 0,
				},
			};
		},
		methods: {
			changeBulanRealisasi(bulan_realisasi) {
				this.bulan_realisasi = bulan_realisasi;
				this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
					bulan_realisasi
				);
			},
			fetchOPD: async function() {
				await this.$ajax
					.post(
						"/dmaster/opd",
						{
							tahun: this.$store.getters["uifront/getTahunAnggaran"],
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_opd = data.opd;
						this.datatableLoaded = true;
					});
			},
			async loadunitkerja() {
				await this.$ajax
					.get("/dmaster/opd/" + this.OrgID_Selected + "/unitkerja", {
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(({ data }) => {
						this.DataOPD = data.organisasi;
						this.daftar_unitkerja = data.unitkerja;
						this.datatableLoaded = true;
					});
			},
			async loaddatakegiatan() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/renjamurni/report/formbunitkerja",
						{
							tahun: this.$store.getters["uifront/getTahunAnggaran"],
							no_bulan: this.bulan_realisasi,
							SOrgID: this.SOrgID_Selected,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.DataUnitKerja = data.unitkerja;
						this.datatable = data.rka;
						this.total_data = data.total_data;
						this.datatableLoaded = false;
						this.datatableLoading = false;
					});
			},
			colorRowFormA(item) {
				var color = "";
				if (item.isprogram == 1) {
					color = "lime lighten-3";
				} else if(item.iskegiatan == 1) {
					color = "lime lighten-4";
				} else if (item.issubkegiatan == 1) {
					color = "white";
				} else {
					color = "white";
				}				
				return color;
			},
			fontWeight(item) {
				var weight = "";
				if (item.isprogram == 1) {
					weight = "font-weight-bold";
				} else if(item.iskegiatan == 1) {
					weight = "font-weight-medium";
				} else if (item.issubkegiatan == 1) {
					weight = "Normal weight text";
				} else {
					weight = "Normal weight text";
				}				
				return weight;
			},
		},
		watch: {
			OrgID_Selected(val) {
				var page = this.$store.getters["uiadmin/Page"]("formamurni");
				if (this.firstloading == true && val.length > 0) {
					page.OrgID_Selected = val;
					this.$store.dispatch("uiadmin/updatePage", page);
					this.loadunitkerja();
				} else if (this.firstloading == false && val.length > 0) {
					page.OrgID_Selected = val;
					this.$store.dispatch("uiadmin/updatePage", page);
					this.loadunitkerja();

					this.datatableLoaded = false;
					this.datatable = [];
				}
			},
			SOrgID_Selected(val) {
				var page = this.$store.getters["uiadmin/Page"]("formamurni");
				if (this.firstloading == false && val.length > 0) {
					this.datatableLoaded = false;
				}
				page.SOrgID_Selected = val;
				this.$store.dispatch("uiadmin/updatePage", page);
				this.loaddatakegiatan();
			},
		},
		components: {
			RenjaMurniLayout,
			ModuleHeader,
			Filter2
		},
	};
</script>