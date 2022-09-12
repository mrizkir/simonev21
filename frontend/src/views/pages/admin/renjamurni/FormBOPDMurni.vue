<template>
	<RenjaMurniLayout :temporaryleftsidebar="true">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				FORM B OPD MURNI
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
					Laporan Form B OPD Rencana Kegiatan dan Anggaran (RKA) OPD APBD Murni
					s.d
					<strong>
						BULAN {{ nama_bulan }} T.A
						{{ $store.getters["auth/TahunSelected"] }}
					</strong>
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
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
							{{ $store.getters["auth/TahunSelected"] }}
						</strong>
					</v-alert>
				</v-col>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						:search="search"
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
								<v-toolbar-title>DAFTAR SUB KEGIATAN</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-btn
									color="primary"
									fab
									small
									@click.stop="printtoexcel"
									:disabled="btnLoading || datatableLoaded"
								>
									<v-icon>mdi-printer</v-icon>
								</v-btn>
							</v-toolbar>
						</template>
						<template v-slot:item.bobot1="{ item }">
							{{ item.bobot1 | makeLookPrecision }}
						</template>
						<template v-slot:item.fisik_target1="{ item }">
							{{ item.fisik_target1 | makeLookPrecision }}
						</template>
						<template v-slot:item.fisik_realisasi1="{ item }">
							{{ item.fisik_realisasi1 | makeLookPrecision }}
						</template>
						<template v-slot:item.fisik_ttb1="{ item }">
							{{ item.fisik_ttb1 | makeLookPrecision }}
						</template>
						<template v-slot:item.keuangan_target_persen_1="{ item }">
							{{ item.keuangan_target_persen_1 | makeLookPrecision }}
						</template>
						<template v-slot:item.keuangan_realisasi_persen_1="{ item }">
							{{ item.keuangan_realisasi_persen_1 | makeLookPrecision }}
						</template>
						<template v-slot:item.keuangan_ttb1="{ item }">
							{{ item.keuangan_ttb1 | makeLookPrecision }}
						</template>
						<template v-slot:item.sisa_anggaran_persen="{ item }">
							{{ item.sisa_anggaran_persen | makeLookPrecision }}
						</template>
						<template v-slot:body="{ items }">
							<tbody>
								<tr
									v-for="item in items"
									v-bind:key="item.FormBMurniID"
									:class="[colorRowFormA(item), fontWeight(item)]"
								>
									<td>{{ item.kode }}</td>
									<td>{{ item.nama_uraian }}</td>
									<td class="text-right">
										{{ item.pagu_dana1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.bobot1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.fisik_target1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.fisik_realisasi1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.fisik_ttb1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.keuangan_target1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.keuangan_target_persen_1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.keuangan_realisasi1 | formatUang }}
									</td>
									<td class="text-right">
										{{ item.keuangan_realisasi_persen_1 | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ item.keuangan_ttb1 | makeLookPrecision }}
									</td>
									<td class="text-left">
										{{ item.lokasi }}
									</td>
									<td class="text-right">
										{{ item.sisa_anggaran | formatUang }}
									</td>
									<td class="text-right">
										{{ item.sisa_anggaran_persen | makeLookPrecision }}
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="orange font-weight-bold dark">
									<td colspan="2" class="text-right">TOTAL</td>
									<td class="text-right">
										{{ total_data.totalPaguOPD | formatUang }}
									</td>
									<td class="text-right">
										{{ total_data.totalPersenBobot | makeLookPrecision }}
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
										{{ total_data.total_ttb_fisik | makeLookPrecision }}
									</td>
									<td class="text-right">
										{{ total_data.totalTargetKeuanganKeseluruhan | formatUang }}
									</td>
									<td class="text-right">
										{{
											total_data.totalPersenTargetKeuangan | makeLookPrecision
										}}
									</td>
									<td class="text-right">
										{{
											total_data.totalRealisasiKeuanganKeseluruhan | formatUang
										}}
									</td>
									<td class="text-right">
										{{
											total_data.totalPersenRealisasiKeuangan
												| makeLookPrecision
										}}
									</td>
									<td class="text-right">
										{{ total_data.total_ttb_keuangan | makeLookPrecision }}
									</td>
									<td class="text-center"></td>
									<td class="text-right">
										{{ total_data.totalSisaAnggaran | formatUang }}
									</td>
									<td class="text-right">
										{{ total_data.totalPersenSisaAnggaran | makeLookPrecision }}
									</td>
								</tr>
							</tfoot>
						</template>
						<template v-slot:no-data>
							Belum ada kegiatan.
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
		name: "FormBOPDMurni",
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
					href: "/belanjamurni",
				},
				{
					text: "LAPORAN",
					disabled: false,
					href: "#",
				},
				{
					text: "FORM B OPD",
					disabled: true,
					href: "#",
				},
			];
			this.bulan_realisasi = this.$store.getters["uifront/getBulanRealisasi"];
			this.nama_bulan = this.$store.getters["uifront/getNamaBulan"](
				this.bulan_realisasi
			);

			this.$store.dispatch("uiadmin/addToPages", {
				name: "formbopdmurni",
				OrgID_Selected: "",
			});
		},
		mounted() {
			this.fetchOPD();
			var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
				"formbopdmurni",
				"OrgID_Selected"
			);
			if (OrgID_Selected.length > 0) {
				this.OrgID_Selected = OrgID_Selected;
				this.firstloading = false;
				this.$refs.filter2.setFirstTimeLoading(this.firstloading);
			}
		},
		data() {
			return {
				btnLoading: false,
				firstloading: true,
				bulan_realisasi: null,
				nama_bulan: null,

				//filter form
				daftar_opd: [],
				OrgID_Selected: "",
				//Organisasi
				DataOPD: null,

				//data table
				datatableLoaded: true,
				datatableLoading: false,
				expanded: [],
				datatable: [],
				headers: [
					{ text: "KODE", value: "kode", width: 80, sortable: false },
					{
						text: "PROGRAM/KEGIATAN/SUB KEGIATAN",
						value: "nama_uraian",
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
				search: "",
				total_data: {
					totalPaguOPD: 0,
					totalTargetKeuanganKeseluruhan: 0,
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
				this.loaddatakegiatan();
			},
			fetchOPD: async function() {
				await this.$ajax
					.post(
						"/dmaster/opd",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
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
			loaddatakegiatan: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/renjamurni/report/formbopd",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
							no_bulan: this.bulan_realisasi,
							OrgID: this.OrgID_Selected,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
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
				} else if (item.iskegiatan == 1) {
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
				} else if (item.iskegiatan == 1) {
					weight = "font-weight-medium";
				} else if (item.issubkegiatan == 1) {
					weight = "Normal weight text";
				} else {
					weight = "Normal weight text";
				}
				return weight;
			},
			printtoexcel: async function() {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/renjamurni/report/formbopd/printtoexcel",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
							no_bulan: this.bulan_realisasi,
							OrgID: this.OrgID_Selected,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
							responseType: "arraybuffer",
						}
					)
					.then(({ data }) => {
						const url = window.URL.createObjectURL(new Blob([data]));
						const link = document.createElement("a");
						link.href = url;

						link.setAttribute("download", "form_b_" + Date.now() + ".xlsx");
						document.body.appendChild(link);
						link.click();
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
		},
		watch: {
			OrgID_Selected(val) {
				var page = this.$store.getters["uiadmin/Page"]("formbopdmurni");
				if (this.firstloading == false && val.length > 0) {
					this.datatableLoaded = false;
				}
				page.OrgID_Selected = val;
				this.$store.dispatch("uiadmin/updatePage", page);
				this.loaddatakegiatan();
			},
		},
		components: {
			RenjaMurniLayout,
			ModuleHeader,
			Filter2,
		},
	};
</script>
