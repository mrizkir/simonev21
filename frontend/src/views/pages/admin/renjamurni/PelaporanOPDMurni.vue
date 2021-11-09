<template>
	<RenjaMurniLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-file-document
			</template>
			<template v-slot:name>
				PELAPORAN OPD
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
					Pelaporan laporan Form B oleh OPD yang telah di acc oleh Kepala.
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
					<v-data-table
						:headers="headers"
						:items="datatable"
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
								<v-toolbar-title>DAFTAR PELAPORAN</v-toolbar-title>
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
											:disabled="btnLoading || !(OrgID_Selected.length > 0)"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>TAMBAH PELAPORAN</span>
								</v-tooltip>
								<v-dialog
									v-model="dialogfrm"
									max-width="800px"
									persistent
									v-if="dialogfrm"
								>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													TAMBAH PELAPORAN
												</span>
											</v-card-title>
											<v-card-subtitle>
												{{ DataOPD.kode_organisasi }} /
												{{ DataOPD.Nm_Organisasi }}
											</v-card-subtitle>
											<v-card-text>
												<v-alert type="info">
													Data pelaporan ini diperoleh dari hasil perhitungan di
													Form B.
												</v-alert>
											</v-card-text>
											<v-card-text>
												<v-select
													label="BULAN LAPORAN"
													v-model="BulanLaporan"
													:items="daftar_bulan"
													:rules="rule_bulan"
													outlined
													dense
												/>
												<v-currency-field
													label="PAGU DANA:"
													:min="null"
													:max="null"
													outlined
													:disabled="true"
													v-model="formdata.PaguDana"
													dense
												>
												</v-currency-field>
												<v-currency-field
													label="REALISASI KEUANGAN:"
													:min="null"
													:max="null"
													outlined
													:disabled="true"
													v-model="formdata.RealisasiKeuangan"
													dense
												>
												</v-currency-field>
												<v-text-field
													v-model="formdata.RealisasiFisik"
													label="REALISASI FISIK:"
													:disabled="true"
													outlined
													dense
												/>
												<v-currency-field
													label="KONTRAK:"
													:min="null"
													:max="null"
													outlined
													:disabled="true"
													v-model="formdata.Kontrak"
													dense
												>
												</v-currency-field>
												<v-text-field
													v-model="formdata.PekerjaanSelesai"
													label="PEKERJAAN SELESAI:"
													:disabled="true"
													outlined
													hint="sub kegiatan yang nilai persentase keuangan sudah mencapai 100"
													persistent-hint
													dense
												/>
												<v-text-field
													v-model="formdata.PekerjaanBerjalan"
													label="PEKERJAAN BERJALAN:"
													:disabled="true"
													outlined
													hint="sub kegiatan yang nilai persentase keuangan 0 > n < 100"
													persistent-hint
													dense
												/>
												<v-text-field
													v-model="formdata.PekerjaanTerhenti"
													label="PEKERJAAN TERHENTI:"
													:disabled="true"
													outlined
													dense
													hint="sub kegiatan yang dinyatakan berhenti, di data RKA"
													persistent-hint
												/>
												<v-text-field
													v-model="formdata.PekerjaanBelumBerjalan"
													label="PEKERJAAN BELUM BERJALAN:"
													:disabled="true"
													outlined
													dense
													hint="sub kegiatan yang dinyatakan nilai persentase keuangan = 0"
													persistent-hint
												/>
												<v-file-input
													accept="application/pdf"
													label="BUKTI CETAK LAPORAN FORM B"
													:rules="rule_bukti_cetak"
													show-size
													v-model="formdata.bukti_cetak"
													dense
													outlined
												>
												</v-file-input>
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
										:disabled="item.PaguDana1 > 0 || item.Locked || btnLoading"
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
						<template v-slot:item.PaguDana1="{ item }">
							{{ item.PaguDana1 | formatUang }}
						</template>
						<template v-slot:item.RealisasiKeuangan1="{ item }">
							{{ item.RealisasiKeuangan1 | formatUang }}
						</template>
						<template v-slot:item.SisaAnggaran="{ item }">
							{{ (item.PaguDana1 - item.RealisasiKeuangan1) | formatUang }}
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12" class="mb1">
									<strong>ID:</strong>{{ item.RKAID }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
								<v-col cols="12" class="mb1 text-center">
									<v-btn
										color="blue darken-1"
										text
										@click.stop="resetdatakegiatan(item)"
										:disabled="btnLoading"
									>
										RESET
									</v-btn>
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
							Belum ada pelaporan bulanan OPD.
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
	</RenjaMurniLayout>
</template>
<script>
	import RenjaMurniLayout from "@/views/layouts/RenjaMurniLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "PelaporanOPDMurni",
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
					text: "PELAPORAN OPD",
					disabled: true,
					href: "#",
				},
			];
			this.$store.dispatch("uiadmin/addToPages", {
				name: "pelaporanopdmurni",
				OrgID_Selected: "",
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
				"pelaporanopdmurni",
				"OrgID_Selected"
			);
			if (OrgID_Selected.length > 0) {
				this.OrgID_Selected = OrgID_Selected;
			}
			this.firstloading = false;
		},
		data() {
			return {
				firstloading: true,
				expanded: [],
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
						value: "PaguDana1",
						align: "end",
						width: 100,
					},
					{
						text: "REALISASI FISIK",
						value: "RealisasiFisik1",
						align: "end",
						width: 100,
					},
					{
						text: "REALISASI KEUANGAN",
						value: "RealisasiKeuangan1",
						align: "end",
						width: 100,
					},
					{ text: "%", align: "end", value: "persen_keuangan1", width: 50 },
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
				//Organisasi
				DataOPD: null,
				//dialog
				dialogfrm: false,
				//form data
				form_valid: true,
				daftar_bulan: [],
				BulanLaporan: null,
				formdata: {
					Statistik3ID: null,
					BulanLaporan: null,
					PaguDana: 0,
					RealisasiKeuangan: 0,
					RealisasiFisik: 0,
					Kontrak: 0,
					PekerjaanSelesai: 0,
					PekerjaanBerjalan: 0,
					PekerjaanTerhenti: 0,
					PekerjaanBelumBerjalan: 0,
					bukti_cetak: null,
				},
				formdefault: {
					Statistik3ID: null,
					BulanLaporan: null,
					PaguDana: 0,
					RealisasiKeuangan: 0,
					RealisasiFisik: 0,
					Kontrak: 0,
					PekerjaanSelesai: 0,
					PekerjaanBerjalan: 0,
					PekerjaanTerhenti: 0,
					PekerjaanBelumBerjalan: 0,
					bukti_cetak: null,
				},
				rule_bulan: [
					value => !!value || "Mohon untuk di pilih bulan pelaporan !!!",
				],
				rule_bukti_cetak: [
					value => !!value || "Mohon sertakan file laporan !!!",
					value => {
						if (value && typeof value !== "undefined" && value.length > 0) {
							return (
								value.size < 10000000 ||
								"File Bukti Bayar harus kurang dari 10MB."
							);
						} else {
							return true;
						}
					},
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
						var num = new Number(data[i].PaguDana1);
						totalpagukegiatan += num;
					}
					summary.paguunitkerja = this.DataUnitKerja.PaguDana1;
					summary.pagukegiatan = totalpagukegiatan;
					var totalrealisasi = parseFloat(
						this.DataUnitKerja.RealisasiKeuangan1
					);
					summary.realisasi = totalrealisasi;
					summary.sisa = totalpagukegiatan - totalrealisasi;
					summary.persen_keuangan =
						totalrealisasi > 0 && totalpagukegiatan > 0
							? (totalrealisasi / totalpagukegiatan) * 100
							: 0;
					summary.fisik = this.DataUnitKerja.RealisasiFisik1;
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
			loaddatauraianfirsttime: async function(item) {
				if (!item.PaguDana2 > 0) {
					this.btnLoading = true;
					await this.$ajax
						.post(
							"/renja/rkamurni/loaddatauraianfirsttime",
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
			loaddatapelaporan: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/renja/pelaporanopdmurni",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
							OrgID: this.OrgID_Selected,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.DataOPD = data.data_opd;
						this.datatable = data.laporanopd;
						this.datatableLoaded = true;
						this.datatableLoading = false;
						// this.footersummary();
					});
			},
			async addItem() {
				await this.$ajax
					.get(
						"/renja/pelaporanopdmurni/bulanpelaporan/" + this.OrgID_Selected,
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						let daftarbulan = data.bulan;
						var bulan = [];
						var index = 0;
						for (var i in daftarbulan) {
							bulan[index] = daftarbulan[i];
							index += 1;
						}
						this.daftar_bulan = bulan;
					});
				this.dialogfrm = true;
			},
			save() {
				if (this.$refs.frmdata.validate()) {					
					this.btnLoading = true;
					// if (this.editedIndex > -1) {
				// 		this.$ajax
				// 			.post(
				// 				"/renja/pelaporanopdmurni/" + this.formdata.Statistik3ID,
				// 				{
				// 					_method: "put",
				// 					PaguDana: this.datakegiatan.PaguDana,
				// 					RealisasiKeuangan: this.RealisasiKeuangan,
				// 					RealisasiFisik: this.formdata.RealisasiFisik,
				// 					Kontrak: this.formdata.Kontrak,
				// 					PekerjaanSelesai: this.formdata.PekerjaanSelesai,
				// 					PekerjaanBerjalan: this.formdata.PekerjaanBerjalan,
				// 					PekerjaanTerhenti: this.formdata.PekerjaanTerhenti,
				// 					PekerjaanBelumBerjalan: this.formdata.PekerjaanBelumBerjalan,
				// 					bukti_cetak: this.datakegiatan.bukti_cetak,
				// 				},
				// 				{
				// 					headers: {
				// 						Authorization: this.$store.getters["auth/Token"],
				// 					},
				// 				}
				// 			)
				// 			.then(() => {
				// 				this.initialize();
				// 				this.closedialogfrmedit();
				// 			})
				// 			.catch(() => {
				// 				this.btnLoading = false;
				// 			});
					// } else {
						this.$ajax
							.post(
								"/renja/pelaporanopdmurni/store",
								{
									OrgID: this.datauraian.OrgID_Selected,
									BulanLaporan: this.datauraian.BulanLaporan,
									PaguDana: this.datakegiatan.PaguDana,
									RealisasiKeuangan: this.RealisasiKeuangan,
									RealisasiFisik: this.formdata.RealisasiFisik,
									Kontrak: this.formdata.Kontrak,
									PekerjaanSelesai: this.formdata.PekerjaanSelesai,
									PekerjaanBerjalan: this.formdata.PekerjaanBerjalan,
									PekerjaanTerhenti: this.formdata.PekerjaanTerhenti,
									PekerjaanBelumBerjalan: this.formdata.PekerjaanBelumBerjalan,
									bukti_cetak: this.datakegiatan.bukti_cetak,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(() => {
								this.initialize();
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					// }
				}
			},
			viewUraian(item) {
				var page = this.$store.getters["uiadmin/Page"]("pelaporanopdmurni");
				if (page.datakegiatan.RKAID == "") {
					page.datakegiatan = item;
					this.$store.dispatch("uiadmin/updatePage", page);
					this.$router.push(
						"/renjamurni/rka/uraian/" + page.datakegiatan.RKAID
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
									"/renjamurni/rka/uraian/" + page.datakegiatan.RKAID
								);
							}
						});
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data RKA Murni dengan Nama " +
							item.Nm_Sub_Kegiatan +
							" ?",
						{ color: "red", width: "600px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/renja/rkamurni/" + item.RKAID,
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
			resetdatakegiatan(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin mengeset ulang jumlah pagu, jumlahrealisasi fisik dan keuangan untuk RKA dengan kode " +
							item.kode_sub_kegiatan +
							" ?",
						{ color: "red", width: "600px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/renja/rkamurni/resetdatakegiatan/" + item.RKAID,
									{
										_method: "PUT",
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
					this.dialogfrm = false;
				}, 300);
			},
		},
		watch: {
			OrgID_Selected(val) {
				var page = this.$store.getters["uiadmin/Page"]("pelaporanopdmurni");
				if (this.firstloading == false && val.length > 0) {
					this.datatableLoaded = false;
				}
				page.OrgID_Selected = val;
				this.$store.dispatch("uiadmin/updatePage", page);
				this.loaddatapelaporan();
			},
			BulanLaporan(val) {
				this.$ajax
					.post(
						"/renjamurni/report/formbopd",
						{
							tahun: this.$store.getters["auth/TahunSelected"],
							no_bulan: val,
							OrgID: this.OrgID_Selected,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.formdata.PaguDana = data.total_data.totalPaguOPD;
						this.formdata.RealisasiKeuangan =
							data.total_data.totalRealisasiKeuanganKeseluruhan;
						this.formdata.RealisasiFisik =
							data.total_data.totalPersenRealisasiFisik;
						this.formdata.Kontrak = 0;
					});
			},
		},
		components: {
			RenjaMurniLayout,
			ModuleHeader,
		},
	};
</script>
