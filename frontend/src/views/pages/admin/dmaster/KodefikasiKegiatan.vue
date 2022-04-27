<template>
	<DataMasterLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-group
			</template>
			<template v-slot:name>
				KEGIATAN
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
					Daftar "kegiatan" sesuai dengan Keputusan Menteri Dalam Negeri No.
					050-3708 tentang pemutakhiran, klasifikasi, kodefikasi, perencanaan,
					dan pembangunan daerah.
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-text>
							<v-text-field
								v-model="search"
								append-icon="mdi-database-search"
								label="Search"
								single-line
								filled
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
						item-key="KgtID"
						show-expand
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
											@click.stop="copyItem"
											:disabled="
												!$store.getters['auth/can'](
													'DMASTER-KODEFIKASI-KEGIATAN_STORE'
												)
											"
										>
											<v-icon>mdi-reload</v-icon>
										</v-btn>
									</template>
									<span>
										Salin kegiatan ke T.A
										{{ $store.getters["auth/TahunSelected"] }}
									</span>
								</v-tooltip>
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
											:disabled="
												!$store.getters['auth/can'](
													'DMASTER-KODEFIKASI-KEGIATAN_STORE'
												)
											"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>Tambah Kegiatan</span>
								</v-tooltip>
								<v-dialog v-model="dialogcopyfrm" max-width="500px" persistent>
									<v-form
										ref="frmcopydata"
										v-model="form_salin_valid"
										lazy-validation
									>
										<v-card>
											<v-card-title>
												<span class="headline">
													Salin Kegiatan ke T.A
													{{ $store.getters["auth/TahunSelected"] }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-alert type="warning">
													Menghindari duplikat proses salin, akan menghapus terlebih dahulu data kegiatan T.A {{ $store.getters["auth/TahunSelected"] }}
												</v-alert>
												<v-select
													label="DARI TAHUN ANGGARAN"
													v-model="tahunasal"
													:items="daftar_ta"
													:rules="rule_tahun_asal"
													outlined
													dense
												/>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="closedialogcopyfrm"
												>
													TUTUP
												</v-btn>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="salinkegiatan"
													:disabled="!form_salin_valid || btnLoading"
												>
													SALIN
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
								<v-dialog v-model="dialogfrm" max-width="800px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													{{ formtitle }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-select
													v-model="formdata.PrgID"
													:items="daftar_program"
													item-text="nama_program"
													item-value="PrgID"
													label="PROGRAM"
													:rules="rule_program"
													single-line
													filled
													outlined
												>
												</v-select>
												<v-text-field
													v-model="formdata.Kd_Kegiatan"
													label="KODE KEGIATAN"
													filled
													outlined
													:rules="rule_kode"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Nm_Kegiatan"
													label="NAMA KEGIATAN"
													filled
													:rules="rule_name"
													outlined
												>
												</v-text-field>
												<v-textarea
													v-model="formdata.Descr"
													label="KETERANGAN"
													filled
													outlined
												>
												</v-textarea>
												<v-switch v-model="formdata.Locked" label="AKTIF" />
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
								<v-dialog
									v-model="dialogdetailitem"
									max-width="800px"
									persistent
								>
									<v-card>
										<v-card-title>
											<span class="headline">
												DETAIL DATA
											</span>
										</v-card-title>
										<v-card-text>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															ID
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.KgtID }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															PROGRAM
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Program }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															KODE KEGIATAN
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.kode_kegiatan }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															KETERANGAN
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Descr }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															NAMA KEGIATAN
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Kegiatan }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															CREATED/UPDATED
														</v-card-title>
														<v-card-subtitle>
															{{
																$date(formdata.updated_at).format(
																	"DD/MM/YYYY HH:mm"
																)
															}}/
															{{
																$date(formdata.created_at).format(
																	"DD/MM/YYYY HH:mm"
																)
															}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
										</v-card-text>
										<v-card-actions>
											<v-spacer></v-spacer>
											<v-btn
												color="blue darken-1"
												text
												@click.stop="closedialogdetailitem"
											>
												KELUAR
											</v-btn>
										</v-card-actions>
									</v-card>
								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.Locked="{ item }">
							<v-chip class="ma-2" :color="item.Locked == 1 ? 'red' : 'success'" outlined small>
								{{ item.Locked == 1 ? "TIDAK AKTIF" : "AKTIF" }}
							</v-chip>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										v-bind="attrs"
										v-on="on"
										small
										class="mr-2"
										@click.stop="viewItem(item)"
									>
										mdi-eye
									</v-icon>
								</template>
								<span>Detail Kegiatan</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										v-bind="attrs"
										v-on="on"
										small
										class="mr-2"
										@click.stop="editItem(item)"
										:disabled="
											!$store.getters['auth/can'](
												'DMASTER-KODEFIKASI-KEGIATAN_UPDATE'
											)
										"
									>
										mdi-pencil
									</v-icon>
								</template>
								<span>Ubah Kegiatan</span>
							</v-tooltip>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										v-bind="attrs"
										v-on="on"
										small
										color="red darken-1"
										:disabled="
											btnLoading ||
												!$store.getters['auth/can'](
													'DMASTER-KODEFIKASI-KEGIATAN_STORE'
												)
										"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus Kegiatan</span>
							</v-tooltip>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>{{ item.KgtID }}
								<strong>created_at:</strong>
								{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								<strong>updated_at:</strong>
								{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
							</td>
						</template>
						<template v-slot:no-data>
							Data belum tersedia
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
	</DataMasterLayout>
</template>
<script>
	import DataMasterLayout from "@/views/layouts/DataMasterLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "KodefikasiKegiatan",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "DATA MASTER",
					disabled: false,
					href: "#",
				},
				{
					text: "KODEFIKASI",
					disabled: true,
					href: "#",
				},
				{
					text: "KEGIATAN",
					disabled: true,
					href: "#",
				},
			];
			this.initialize();
		},
		data() {
			return {
				btnLoading: false,
				datatableLoading: false,
				expanded: [],
				datatable: [],
				headers: [
					{ text: "KODE KEGIATAN", value: "kode_kegiatan", width: 100 },
					{ text: "NAMA KEGIATAN", value: "Nm_Kegiatan", width: 250 },
					{ text: "PROGRAM", value: "Nm_Program", width: 170 },
					{ text: "KET", value: "Descr", width: 140 },
					{ text: "TA", value: "TA", width: 70 },
					{ text: "STATUS", value: "Locked", align: "center", width: 120 },
					{ text: "AKSI", value: "actions", sortable: false, width: 100 },
				],
				search: "",
				//dialog
				dialogcopyfrm: false,
				dialogfrm: false,
				dialogdetailitem: false,
				//form data
				form_valid: true,
				form_salin_valid: true,
				daftar_program: [],
				formdata: {
					KgtID: "",
					PrgID: "",
					Kd_Kegiatan: "",
					Nm_Kegiatan: "",
					Descr: "",
					TA: "",
					Locked: 0,
					created_at: "",
					updated_at: "",
				},
				formdefault: {
					KgtID: "",
					PrgID: "",
					Kd_Kegiatan: "",
					Nm_Kegiatan: "",
					Descr: "",
					TA: "",
					Locked: 0,
					created_at: "",
					updated_at: "",
				},
				editedIndex: -1,
				//salin kegiatan
				tahunasal: null,
				daftar_ta: [],
				//form rules
				rule_program: [value => !!value || "Mohon untuk di pilih Program !!!"],
				rule_kode: [
					value => !!value || "Mohon untuk di isi Kode Kegiatan!!!",
					value =>
						/^[0-9]*\.?[0-9]*$/.test(value) ||
						"Kode Kegiatan hanya boleh angka",
					value => value.length > 1 || "Kode Kegiatan minimal 2 angka",
				],
				rule_name: [value => !!value || "Mohon untuk di isi Nama Kegiatan !!!"],
				//form rules salin kegiatan
				rule_tahun_asal: [
					value =>
						!!value || "Mohon untuk dipilih Tahun Anggaran sebelumnya!!!",
					value =>
						value < this.$store.getters["auth/TahunSelected"] ||
						"Tahun asal harus lebih kecil dari " +
							this.$store.getters["auth/TahunSelected"],
				],
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/dmaster/kodefikasi/kegiatan",
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
						this.datatable = data.kodefikasikegiatan;
						this.datatableLoading = false;
					});
			},
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			async addItem() {
				await this.$ajax
					.post(
						"/dmaster/kodefikasi/program",
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
						this.daftar_program = data.kodefikasiprogram;
						this.dialogfrm = true;
						this.formdata.Locked = 1;
					});
			},
			copyItem() {
				this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
				this.dialogcopyfrm = true;
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				await this.$ajax
					.post(
						"/dmaster/kodefikasi/program",
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
						this.daftar_program = data.kodefikasiprogram;
						this.formdata = Object.assign({}, item);
						this.formdata.Locked = item.Locked == 0 ? 1 : 0;
						this.dialogfrm = true;
					});
			},
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			salinkegiatan() {
				if (this.$refs.frmcopydata.validate()) {
					this.$ajax
						.post(
							"/dmaster/kodefikasi/kegiatan/salin",
							{
								tahun_asal: this.tahunasal,
								tahun_tujuan: this.$store.getters["auth/TahunSelected"],
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(() => {
							this.$router.go();
							this.closedialogcopyfrm();
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					var locked = this.formdata.Locked == 1 ? 0 : 1;
					if (this.editedIndex > -1) {						
						this.$ajax
							.post(
								"/dmaster/kodefikasi/kegiatan/" + this.formdata.KgtID,
								{
									_method: "PUT",
									PrgID: this.formdata.PrgID,
									Kd_Kegiatan: this.formdata.Kd_Kegiatan,
									Nm_Kegiatan: this.formdata.Nm_Kegiatan,
									Descr: this.formdata.Descr,
									Locked: locked,
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
					} else {
						this.$ajax
							.post(
								"/dmaster/kodefikasi/kegiatan/store",
								{
									PrgID: this.formdata.PrgID,
									Kd_Kegiatan: this.formdata.Kd_Kegiatan,
									Nm_Kegiatan: this.formdata.Nm_Kegiatan,
									Descr: this.formdata.Descr,
									TA: this.$store.getters["auth/TahunSelected"],
									Locked: locked,
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
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data kegiatan dengan ID " +
							item.KgtID +
							" ?",
						{ color: "red", width: "500px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/kodefikasi/kegiatan/" + item.KgtID,
									{
										_method: "DELETE",
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									const index = this.datatable.indexOf(item);
									this.datatable.splice(index, 1);
									this.btnLoading = false;
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			closedialogfrm() {
				this.btnLoading = false;
				this.dialogfrm = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
					this.$refs.frmdata.reset();
				}, 300);
			},
			closedialogdetailitem() {
				this.dialogdetailitem = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
				}, 300);
			},
		},
		computed: {
			formtitle() {
				return this.editedIndex === -1 ? "TAMBAH DATA" : "UBAH DATA";
			},
		},
		components: {
			DataMasterLayout,
			ModuleHeader,
		},
	};
</script>
