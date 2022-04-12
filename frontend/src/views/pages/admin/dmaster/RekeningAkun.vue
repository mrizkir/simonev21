<template>
	<DataMasterLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-group
			</template>
			<template v-slot:name>
				REKENING AKUN
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
					Daftar "rekening akun" sesuai dengan Keputusan Menteri Dalam Negeri
					No. 050-3708 tentang pemutakhiran, klasifikasi, kodefikasi,
					perencanaan, dan pembangunan daerah.
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
						item-key="AkunID"
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
								<v-toolbar-title>DAFTAR AKUN</v-toolbar-title>
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
													'DMASTER-KODEFIKASI-REKENING-AKUN_STORE'
												)
											"
										>
											<v-icon>mdi-reload</v-icon>
										</v-btn>
									</template>
									<span>
										Salin Rekning Akun ke T.A
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
													'DMASTER-KODEFIKASI-REKENING-AKUN_STORE'
												)
											"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>Tambah Rekening Akun</span>
								</v-tooltip>
								<v-dialog v-model="dialogfrm" max-width="800px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													{{ formtitle }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-text-field
													v-model="formdata.Kd_Rek_1"
													label="KODE AKUN"
													filled
													:rules="rule_kode"
													outlined
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Nm_Akun"
													label="NAMA AKUN"
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
															{{ formdata.AkunID }}
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
															KODE AKUN
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Kd_Rek_1 }}
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
															CREATED
														</v-card-title>
														<v-card-subtitle>
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
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															NAMA AKUN
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Akun }}
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
															UPDATED
														</v-card-title>
														<v-card-subtitle>
															{{
																$date(formdata.updated_at).format(
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
								<v-dialog v-model="dialogcopyfrm" max-width="500px" persistent>
									<v-form
										ref="frmcopydata"
										v-model="form_salin_valid"
										lazy-validation
									>
										<v-card>
											<v-card-title>
												<span class="headline">
													Salin Rekening Akun ke T.A
													{{ $store.getters["auth/TahunSelected"] }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-alert type="warning">
													Menghindari duplikat proses salin, akan menghapus terlebih dahulu data Rekening Akun T.A {{ $store.getters["auth/TahunSelected"] }}
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
													@click.stop="salinrekening"
													:disabled="!form_salin_valid || btnLoading"
												>
													SALIN
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
										v-bind="attrs"
										v-on="on"
										small
										class="mr-2"
										@click.stop="viewItem(item)"
									>
										mdi-eye
									</v-icon>
								</template>
								<span>Detail Rekening Akun</span>
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
												'DMASTER-KODEFIKASI-REKENING-AKUN_UPDATE'
											)
										"
									>
										mdi-pencil
									</v-icon>
								</template>
								<span>Ubah Rekening Akun</span>
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
													'DMASTER-KODEFIKASI-REKENING-AKUN_STORE'
												)
										"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus Rekening Akun</span>
							</v-tooltip>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>{{ item.AkunID }}
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
		name: "RekeningAkun",
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
					text: "REKENING",
					disabled: true,
					href: "#",
				},
				{
					text: "AKUN",
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
					{ text: "KODE AKUN", value: "Kd_Rek_1", width: 150 },
					{ text: "NAMA AKUN", value: "Nm_Akun" },
					{ text: "KET", value: "Descr" },
					{ text: "TA", value: "TA" },
					{ text: "AKSI", value: "actions", sortable: false, width: 100 },
				],
				search: "",
				//dialog
				dialogfrm: false,
				dialogcopyfrm: false,
				dialogdetailitem: false,
				//form data				
				form_salin_valid: true,
				form_valid: true,
				formdata: {
					AkunID: "",
					Kd_Rek_1: "",
					Nm_Akun: "",
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				formdefault: {
					AkunID: "",
					Kd_Rek_1: "",
					Nm_Akun: "",
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				editedIndex: -1,
				//salin akun
				tahunasal: null,
				daftar_ta: [],
				//form rules
				rule_kode: [
					value => !!value || "Mohon untuk di isi Kode Akun!!!",
					value => /^[0-9]+$/.test(value) || "Kode Akun hanya boleh angka",
				],
				rule_name: [
					value => !!value || "Mohon untuk di isi Nama Akun !!!",
				],
				//form rules salin akun
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
						"/dmaster/rekening/akun",
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
						this.datatable = data.akun;
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
			addItem() {
				this.dialogfrm = true;
			},
			editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.formdata = Object.assign({}, item);
				this.dialogfrm = true;
			},
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			copyItem() {
				this.daftar_ta = this.$store.getters["uifront/getDaftarTA"];
				this.dialogcopyfrm = true;
			},
			salinrekening() {
				if (this.$refs.frmcopydata.validate()) {
					this.$ajax
						.post(
							"/dmaster/rekening/akun/salin",
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
					if (this.editedIndex > -1) {
						this.$ajax
							.post(
								"/dmaster/rekening/akun/" + this.formdata.AkunID,
								{
									_method: "PUT",
									Kd_Rek_1: this.formdata.Kd_Rek_1,
									Nm_Akun: this.formdata.Nm_Akun,
									Descr: this.formdata.Descr,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								Object.assign(this.datatable[this.editedIndex], data.akun);
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					} else {
						this.$ajax
							.post(
								"/dmaster/rekening/akun/store",
								{
									Kd_Rek_1: this.formdata.Kd_Rek_1,
									Nm_Akun: this.formdata.Nm_Akun,
									Descr: this.formdata.Descr,
									TA: this.$store.getters["auth/TahunSelected"],
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								this.datatable.push(data.akun);
								this.closedialogfrm();
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
						"Apakah Anda ingin menghapus data urusan dengan ID " +
							item.AkunID +
							" ?",
						{ color: "red", width: "500px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/rekening/akun/" + item.AkunID,
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
			closedialogcopyfrm() {
				this.btnLoading = false;
				this.dialogcopyfrm = false;
				setTimeout(() => {
					this.$refs.frmcopydata.reset();
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
