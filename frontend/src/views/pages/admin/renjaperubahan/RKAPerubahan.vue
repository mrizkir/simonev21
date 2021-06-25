<template>
	<RenjaPerubahanLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-graph
			</template>
			<template v-slot:name>
				RKA PERUBAHAN
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
					Rencana Kegiatan dan Anggaran (RKA) OPD / Unit Kerja APBD Perubahan
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
							>
							</v-text-field>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						:search="search"
						item-key="RKAID"						
						show-expand
						dense
						:expanded.sync="expanded"
						:single-expand="true"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
						@click:row="dataTableRowClicked"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR KEGIATAN</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											v-bind="attrs"
											v-on="on"
											color="primary"
											icon
											outlined
											small
											class="ma-2"
											@click.stop="addItem"
											:disabled="btnLoading || !(SOrgID_Selected.length > 0)"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>TAMBAH RKA</span>
								</v-tooltip>
								<v-dialog v-model="dialogfrm" max-width="800px" persistent v-if="dialogfrm">
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													TAMBAH KEGIATAN
												</span>
											</v-card-title>
											<v-card-subtitle>												
												{{ DataUnitKerja.kode_sub_organisasi }} / {{ DataUnitKerja.Nm_Sub_Organisasi }}												
											</v-card-subtitle>											
											<v-card-text>
												<v-autocomplete
													:items="daftar_bidang"
													v-model="formdata_BidangID"
													label="BIDANG URUSAN"
													item-text="nama_bidang"
													item-value="BidangID"
													filled
													outlined
												>
												</v-autocomplete>
												<v-autocomplete
													:items="daftar_program"
													v-model="formdata_PrgID"
													label="PROGRAM"
													item-text="nama_program"
													item-value="PrgID"
													filled
													outlined
												>
												</v-autocomplete>
												<v-autocomplete
													:items="daftar_kegiatan"
													v-model="formdata_KgtID"
													label="KEGIATAN"
													item-text="nama_kegiatan"
													item-value="KgtID"
													filled
													outlined
												>
												</v-autocomplete>
												<v-autocomplete
													:items="daftar_sub_kegiatan"
													v-model="formdata_SubKgtID"
													label="SUB KEGIATAN"
													item-text="nama_sub_kegiatan"
													item-value="SubKgtID"
													filled
													outlined
													:rules="rule_sub_kegiatan"
												>
												</v-autocomplete>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="closedialogfrm"
												>
													TUTUP
												</v-btn>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="save"
													:disabled="!form_valid || btnLoading"
												>
													SIMPAN
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										small
										v-bind="attrs"
										v-on="on"
										color="primary"
										class="ma-1"
										@click.stop="viewUraian(item)"
									>
										mdi-eye
									</v-icon>
								</template>
								<span>detail uraian kegiatan</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										small
										v-bind="attrs"
										v-on="on"
										class="ma-1"
										color="warning"
										:loading="btnLoading"
										:disabled="item.PaguDana2 > 0 || item.Locked || btnLoading"
										@click.stop="loaddatauraianfirsttime(item)"
									>
										mdi-sync-circle
									</v-icon>
								</template>
								<span>load uraian</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										small
										v-bind="attrs"
										v-on="on"
										color="red"
										:loading="btnLoading"
										:disabled="btnLoading || item.Locked == 1"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus RKA</span>
							</v-tooltip>
							<v-icon small class="mr-2" v-if="item.Locked">
								mdi-lock
							</v-icon>
						</template>
						<template v-slot:item.PaguDana2="{ item }">
							{{ item.PaguDana2 | formatUang }}
						</template>
						<template v-slot:item.RealisasiKeuangan2="{ item }">
							{{ item.RealisasiKeuangan2 | formatUang }}
						</template>
						<template v-slot:item.SisaAnggaran="{ item }">
							{{ (item.PaguDana2 - item.RealisasiKeuangan2) | formatUang }}
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12" clœass="mb1">
									<strong>ID:</strong>{{ item.RKAID }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
							</td>
						</template>
						<template v-slot:body.append>
							<tr class="amber darken-1 font-weight-black">
								<td colspan="3" class="text-right">TOTAL</td>
								<td class="text-right">
									{{ footers.pagukegiatan | formatUang }}
								</td>
								<td class="text-right">{{ footers.fisik }}</td>
								<td class="text-right">{{ footers.realisasi | formatUang }}</td>
								<td class="text-right">
									{{ footers.persen_keuangan.toFixed(2) }}
								</td>
								<td class="text-right">
									{{ footers.sisa | formatUang }}
								</td>
								<td></td>
							</tr>
						</template>
						<template v-slot:no-data>
							<v-btn
								class="ma-2"
								:loading="btnLoading"
								:disabled="showBtnLoadDataKegiatan || btnLoading"
								color="primary"
								@click.stop="loaddatakegiatanFirsttime"
							>
								LOAD DATA KEGIATAN
								<template v-slot:loader>
									<span>LOADING DATA ...</span>
								</template>
							</v-btn>
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
	</RenjaPerubahanLayout>
</template>
<script>
	import RenjaPerubahanLayout from "@/views/layouts/RenjaPerubahanLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "RKAPerubahan",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "RENCANA KERJA PERUBAHAN",
					disabled: false,
					href: "/renjaperubahan",
				},
				{
					text: "RKA PERUBAHAN",
					disabled: true,
					href: "#",
				},
			];
			this.$store.dispatch("uiadmin/addToPages", {
				name: "rkaperubahan",
				OrgID_Selected: "",
				SOrgID_Selected: "",
				datakegiatan: {
					RKAID: "",
				},
				datauraian: {
					RKARincID: "",
				},
				datarekening: {},
			});
		},
		mounted() {
			this.fetchOPD();
			var OrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
				"rkaperubahan",
				"OrgID_Selected"
			);
			var SOrgID_Selected = this.$store.getters["uiadmin/AtributeValueOfPage"](
				"rkaperubahan",
				"SOrgID_Selected"
			);
			if (OrgID_Selected.length > 0) {
				this.OrgID_Selected = OrgID_Selected;
				this.SOrgID_Selected = SOrgID_Selected;
			}
			if (SOrgID_Selected.length > 0) {
				this.OrgID_Selected = OrgID_Selected;
				this.SOrgID_Selected = SOrgID_Selected;
			}
			this.firstloading = false;
		},
		data() {
			return {
				firstloading: true,
				expanded: [],
				search: "",
				btnLoading: false,
				datatableLoading: false,
				datatableLoaded: false,
				datatable: [],
				headers: [
					{ text: "KODE", value: "kode_sub_kegiatan", width: 80 },
					{
						text: "NAMA SUB KEGIATAN",
						value: "Nm_Sub_Kegiatan",
						width: 300,
					},
					{
						text: "PAGU KEGIATAN",
						value: "PaguDana2",
						align: "end",
						width: 100,
					},
					{
						text: "REALISASI FISIK",
						value: "RealisasiFisik2",
						align: "end",
						width: 100,
					},
					{
						text: "REALISASI KEUANGAN",
						value: "RealisasiKeuangan2",
						align: "end",
						width: 100,
					},
					{ text: "%", align: "end", value: "persen_keuangan2", width: 50 },
					{
						text: "SISA PAGU",
						value: "SisaAnggaran",
						align: "end",
						width: 100,
					},
					{ text: "AKSI", value: "actions", sortable: false, width: 110 },
				],
				footers: {
					paguunitkerja: 0,
					pagukegiatan: 0,
					realisasi: 0,
					sisa: 0,
					persen_keuangan: 0,
					fisik: 0,
				},
				//filter form
				daftar_opd: [],
				OrgID_Selected: "",
				daftar_unitkerja: [],
				SOrgID_Selected: "",
				//Organisasi
				DataOPD: null,
				DataUnitKerja: null,
				//dialog
				dialogfrm: false,
				//form data
				form_valid: true,
				daftar_bidang: [],
				daftar_program: [],
				daftar_kegiatan: [],
				daftar_sub_kegiatan: [],
				formdata_BidangID: null,
				formdata_PrgID: null,
				formdata_KgtID: null,
				formdata_SubKgtID: null,
				rule_sub_kegiatan: [
					value => !!value || "Mohon untuk di pilih sub kegiatan !!!",
				],
			};
		},
		methods: {
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			footersummary() {
				let data = this.datatable;
				var summary = {
					paguunitkerja: 0,
					pagukegiatan: 0,
					realisasi: 0,
					sisa: 0,
					persen_keuangan: 0,
					fisik: 0,
				};
				if (data.length > 0) {
					var totalpagukegiatan = 0;
					for (var i = 0; i < data.length; i++) {
						var num = new Number(data[i].PaguDana2);
						totalpagukegiatan += num;
					}
					summary.paguunitkerja = this.DataUnitKerja.PaguDana2;
					summary.pagukegiatan = totalpagukegiatan;
					var totalrealisasi = parseFloat(
						this.DataUnitKerja.RealisasiKeuangan2
					);
					summary.realisasi = totalrealisasi;
					summary.sisa = totalpagukegiatan - totalrealisasi;
					summary.persen_keuangan =
						totalrealisasi > 0 && totalpagukegiatan > 0
							? (totalrealisasi / totalpagukegiatan) * 100
							: 0;
					summary.fisik = this.DataUnitKerja.RealisasiFisik2;
				}
				this.footers = summary;
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
					.then(({ data, status }) => {
						if (status == 200) {
							this.daftar_opd = data.opd;
							this.datatableLoaded = false;
						}
					});
			},
			loadunitkerja: async function() {
				await this.$ajax
					.get("/dmaster/opd/" + this.OrgID_Selected + "/unitkerja", {
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(({ data }) => {
						this.DataOPD = data.organisasi;
						this.daftar_unitkerja = data.unitkerja;
						this.datatableLoaded = false;
					});
			},
			loaddatauraianfirsttime: async function(item) {
				if (!item.PaguDana2 > 0) {
					this.btnLoading = true;
					await this.$ajax
						.post(
							"/renja/rkaperubahan/loaddatauraianfirsttime",
							{
								RKAID: item.RKAID,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(() => {
							this.$router.go();
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			loaddatakegiatanFirsttime: async function() {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/renja/rkaperubahan/loaddatakegiatanfirsttime",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
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
						this.footersummary();
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			loaddatakegiatan: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/renja/rkaperubahan",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
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
						this.datatableLoaded = true;
						this.datatableLoading = false;
						this.footersummary();
					});
			},
			async addItem() {
				this.daftar_bidang.push(
					{
						BidangID: "all",
						nama_bidang: "SEMUA BIDANG URUSAN"
					}
				);
				this.daftar_bidang.push(
					{
						BidangID: this.DataOPD.BidangID_1,
						nama_bidang: "[" + this.DataOPD.kode_bidang_1 + "] " + this.DataOPD.Nm_Bidang_1,
					}
				);
				if (this.DataOPD.BidangID_2) {
					this.daftar_bidang.push(
						{
							BidangID: this.DataOPD.BidangID_2,
							nama_bidang: "[" + this.DataOPD.kode_bidang_2 + "] " + this.DataOPD.Nm_Bidang_2,
						}
					);
				}
				if (this.DataOPD.BidangID_3) {
					this.daftar_bidang.push(
						{
							BidangID: this.DataOPD.BidangID_3,
							nama_bidang: "[" + this.DataOPD.kode_bidang_3 + "] " + this.DataOPD.Nm_Bidang_3,
						}
					);
				}
				this.dialogfrm = true;
			},
			save() {
				if (this.$refs.frmdata.validate()) {					
					this.$ajax
						.post(
							"/renja/rkaperubahan/storekegiatan",
							{
								OrgID: this.DataOPD.OrgID,
								SOrgID: this.DataUnitKerja.SOrgID,
								SubKgtID: this.formdata_SubKgtID,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(() => {
							this.closedialogfrm();
							this.$router.go();
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			viewUraian(item) {
				var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
				if (page.datakegiatan.RKAID == "") {
					page.datakegiatan = item;
					this.$store.dispatch("uiadmin/updatePage", page);
					this.$router.push(
						"/renjaperubahan/rka/uraian/" + page.datakegiatan.RKAID
					);
				} else {
					this.$root.$confirm
						.open(
							"INFO",
							"Kegiatan lain sedang dibuka, jadi tidak bisa membuka kegiatan ini",
							{ color: "warning" }
						)
						.then(confirm => {
							if (confirm) {
								this.$router.push(
									"/renjaperubahan/rka/uraian/" + page.datakegiatan.RKAID
								);
							}
						});
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data RKA Perubahan dengan Nama " +
							item.Nm_Sub_Kegiatan +
							" ?",
						{ color: "red", width: "600px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/renja/rkaperubahan/" + item.RKAID,
									{
										_method: "DELETE",
										pid: "datarka",
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									this.$router.go();
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			closedialogfrm() {
				this.btnLoading = false;
				setTimeout(() => {					
					this.daftar_program = [];
					this.formdata_PrgID = null;

					this.daftar_kegiatan = [];
					this.formdata_KgtID = null;
					
					this.daftar_sub_kegiatan = [];
					this.formdata_SubKgtID = null;

					this.dialogfrm = false;
				}, 300);
			},
		},
		computed: {
			showBtnLoadDataKegiatan() {
				var bool = true;
				if (this.SOrgID_Selected.length > 0 && this.datatableLoaded == true) {
					bool = this.datatable.length > 0;
				}
				return bool;
			},
		},
		watch: {
			OrgID_Selected(val) {
				var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
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
				var page = this.$store.getters["uiadmin/Page"]("rkaperubahan");
				if (this.firstloading == false && val.length > 0) {
					this.datatableLoaded = false;
				}
				page.SOrgID_Selected = val;
				this.$store.dispatch("uiadmin/updatePage", page);
				this.loaddatakegiatan();
			},
			formdata_BidangID(val) {
				this.daftar_program = [];
				this.formdata_PrgID = null;

				this.daftar_kegiatan = [];
				this.formdata_KgtID = null;
				
				this.daftar_sub_kegiatan = [];
				this.formdata_SubKgtID = null;
				
				this.$ajax
					.post(
						"/dmaster/kodefikasi/bidangurusan/" + val + "/program",
						{
							TA: this.$store.getters["auth/TahunSelected"],
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_program = data.program;
						this.dialogfrm = true;
					});
			},
			formdata_PrgID(val) {
				this.daftar_kegiatan = [];
				this.formdata_KgtID = null;
				
				this.daftar_sub_kegiatan = [];
				this.formdata_SubKgtID = null;

				this.$ajax
					.get(
						"/dmaster/kodefikasi/program/" + val + "/kegiatan",
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_kegiatan = data.programkegiatan;
					});
			},
			formdata_KgtID(val) {				
				this.daftar_sub_kegiatan = [];
				this.formdata_SubKgtID = null;
				
				this.$ajax
					.post(
						"/dmaster/kodefikasi/kegiatan/" + val + "/subkegiatanrka",
						{
							SOrgID: this.DataUnitKerja.SOrgID,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_sub_kegiatan = data.subkegiatanrka;
					});						
			},
		},
		components: {
			RenjaPerubahanLayout,
			ModuleHeader,
		},
	};
</script>
