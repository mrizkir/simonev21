<template>
	<DataMasterLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-office-building
			</template>
			<template v-slot:name>
				UNIT KERJA
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
					Halaman untuk mengolah data unit kerja.
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
						item-key="SOrgID"
						sort-by="kode_sub_organisasi"
						show-expand
						:expanded.sync="expanded"
						dense
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
						:single-expand="true"
						class="elevation-1"
						@click:row="dataTableRowClicked"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>
									DAFTAR UNIT KERJA
								</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											v-bind="attrs"
											v-on="on"
											color="warning"
											icon
											outlined
											small
											class="ma-2"
											@click.stop="loadPaguAPBDP"
											:disabled="btnLoading"
										>
											<v-icon>mdi-database-refresh</v-icon>
										</v-btn>
									</template>
									<span>LOAD PAGU APBDP</span>
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
												!$store.getters['auth/can']('DMASTER-UNIT-KERJA_STORE')
											"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>Tambah Unit Kerja</span>
								</v-tooltip>
								<v-dialog v-model="dialogfrm" max-width="700px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													{{ formtitle }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-autocomplete
													:items="daftar_opd"
													v-model="formdata.OrgID"
													label="OPD"
													item-text="Nm_Organisasi"
													item-value="OrgID"
													single-line
													filled
												>
												</v-autocomplete>
												<v-text-field
													v-model="formdata.Kd_Sub_Organisasi"
													label="KODE UNIT KERJA"
													:rules="rule_kode"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Nm_Sub_Organisasi"
													label="NAMA UNIT KERJA"
													:rules="rule_required"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Alias_Sub_Organisasi"
													:rules="rule_required"
													label="SINGKATAN UNIT KERJA"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Alamat"
													:rules="rule_required"
													label="ALAMAT"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.NamaKepalaUnitKerja"
													:rules="rule_kepala_skpd"
													label="NAMA KEPALA UNIT KERJA"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.NIPKepalaUnitKerja"
													:rules="rule_nip_kepala_skpd"
													label="NIP KEPALA UNIT KERJA"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Descr"
													label="KETERANGAN"
													filled
												>
												</v-text-field>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="closedialogfrm"
												>
													BATAL
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
											<span class="headline">DETAIL DATA UNIT KERJA</span>
										</v-card-title>
										<v-card-text>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															<strong>ID:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.SOrgID }}
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
															<strong>ALAMAT:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Alamat }}
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
															<strong>KODE UNIT KERJA:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.kode_sub_organisasi }}
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
															<strong>APBD:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.PaguDana1 | formatUang }}
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
															<strong>NAMA UNIT KERJA:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Sub_Organisasi }}
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
															<strong>APBDP:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.PaguDana2 | formatUang }}
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
															<strong>SINGKATAN:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Alias_Sub_Organisasi }}
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
															<strong>TAHUN:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.TA }}
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
															<strong>BIDANG URUSAN:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Bidang_1 }}
															{{ formdata.Nm_Bidang_2 }}
															{{ formdata.Nm_Bidang_3 }}
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
															<strong>KET.:</strong>
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
															<strong>KEPALA UNIT KERJA:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.NamaKepalaUnitKerja }}
															NIP. {{ formdata.NIPKepalaUnitKerja }}
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
															<strong>CREATED / UPDATED:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{
																$date(formdata.created_at).format(
																	"DD/MM/YYYY HH:mm"
																)
															}}
															/
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
							</v-toolbar>
						</template>
						<template v-slot:item.PaguDana1="{ item }">
							{{ item.PaguDana1 | formatUang }}
						</template>
						<template v-slot:item.Nm_Bidang_1="{ item }">
							{{ item.Nm_Bidang_1 }}
							{{ item.Nm_Bidang_2 }}
							{{ item.Nm_Bidang_3 }}
						</template>
						<template v-slot:item.PaguDana2="{ item }">
							{{ item.PaguDana2 | formatUang }}
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
								<span>Detail Unit Kerja</span>
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
											!$store.getters['auth/can']('DMASTER-UNIT-KERJA_UPDATE')
										"
									>
										mdi-pencil
									</v-icon>
								</template>
								<span>Ubah Unit Kerja</span>
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
													'DMASTER-UNIT-KERJA_DESTROY'
												)
										"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus Unit Kerja</span>
							</v-tooltip>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>{{ item.SOrgID }}
								<strong>created_at:</strong>
								{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								<strong>updated_at:</strong>
								{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
							</td>
						</template>
						<template v-slot:body.append>
							<tr class="amber darken-1 font-weight-black">
								<td colspan="5" class="text-right">TOTAL</td>
								<td class="text-right">
									{{ footers.jumlah_apbd | formatUang }}
								</td>
								<td class="text-right">
									{{ footers.jumlah_apbdp | formatUang }}
								</td>
								<td></td>
							</tr>
						</template>
						<template v-slot:no-data>
							<v-col cols="12">
								Belum ada data unit kerja
							</v-col>
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
		name: "UnitKerja",
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
					text: "UNIT KERJA",
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
				datatableLoaded: false,
				expanded: [],
				datatable: [],
				headers: [
					{ text: "KODE UNIT KERJA", value: "kode_sub_organisasi", width: 200 },
					{ text: "NAMA UNIT KERJA", value: "Nm_Sub_Organisasi", width: 300 },
					{ text: "BIDANG URUSAN", value: "Nm_Bidang_1", width: 200 },
					{
						text: "KEPALA UNIT KERJA",
						value: "NamaKepalaUnitKerja",
						width: 200,
					},
					{ text: "APBD", align: "end", value: "PaguDana1", width: 100 },
					{
						text: "APBDP",
						align: "end",
						value: "PaguDana2",
						sortable: false,
						width: 100,
					},
					{ text: "AKSI", value: "actions", sortable: false, width: 100 },
				],
				search: "",
				footers: {
					jumlah_apbd: 0,
					jumlah_apbdp: 0,
				},
				//dialog
				dialogdetailitem: false,
				dialogfrm: false,
				//form data
				form_valid: true,
				daftar_opd: [],
				formdata: {
					SOrgID: "",
					OrgID: "",
					kode_sub_organisasi: "",
					Kd_Sub_Organisasi: "",
					Nm_Sub_Organisasi: "",
					Alias_Sub_Organisasi: "",
					Alamat: "",
					NamaKepalaUnitKerja: "",
					NIPKepalaUnitKerja: "",
					PaguDana1: 0,
					PaguDana2: 0,
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				formdefault: {
					SOrgID: "",
					OrgID: "",
					kode_sub_organisasi: "",
					Kd_Sub_Organisasi: "",
					Nm_Sub_Organisasi: "",
					Alias_Sub_Organisasi: "",
					Alamat: "",
					NamaKepalaUnitKerja: "",
					NIPKepalaUnitKerja: "",
					PaguDana1: 0,
					PaguDana2: 0,
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				editedIndex: -1,
				//form rules
				rule_required: [
					value => !!value || "Mohon untuk di isi karena dibutuhkan !!!",
				],
				rule_kode: [
					value => !!value || "Mohon untuk di isi Kode Unit Kerja!!!",
					value =>
						/^[0-9]+$/.test(value) || "Kode Unit Kerja hanya boleh angka",
					value => {
						if (value && typeof value !== "undefined" && value.length >= 2) {
							return true;
						} else {
							return "Kode Unit Kerja Kegiatan minimaml 2 angka";
						}
					},
				],
				rule_kepala_skpd: [
					value => !!value || "Mohon untuk di isi nama Kepala Unit Kerja !!!",
					value =>
						/^[A-Za-z\s\\,\\.]*$/.test(value) ||
						"Nama Kepala Unit Kerja hanya boleh string dan spasi",
				],
				rule_nip_kepala_skpd: [
					value => !!value || "Mohon untuk di isi NIP Kepala Unit Kerja !!!",
					value =>
						/^[0-9]+$/.test(value) || "NIP Kepala Unit Kerja hanya boleh angka",
				],
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/dmaster/unitkerja",
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
						var unitkerja = data.unitkerja;
						for (var key in unitkerja) {
							if (unitkerja[key].Kd_Sub_Organisasi < 10) {
								unitkerja[key].Kd_Sub_Organisasi =
									"0" + unitkerja[key].Kd_Sub_Organisasi;
							}
						}
						this.datatable = data.unitkerja;
						this.footers.jumlah_apbd = data.jumlah_apbd;
						this.footers.jumlah_apbdp = data.jumlah_apbdp;
						this.datatableLoaded = true;
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
			loadPaguAPBDP() {
				this.btnLoading = true;
				this.$ajax
					.post(
						"/dmaster/unitkerja/loadpaguapbdp",
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
						this.datatable = data.unitkerja;
						this.footers.jumlah_apbd = data.jumlah_apbd;
						this.footers.jumlah_apbdp = data.jumlah_apbdp;
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			async addItem() {
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
						this.dialogfrm = true;
					});
			},
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.formdata = Object.assign({}, item);
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
						this.dialogfrm = true;
					});
			},
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					if (this.editedIndex > -1) {
						this.$ajax
							.post(
								"/dmaster/unitkerja/" + this.formdata.SOrgID,
								{
									_method: "PUT",
									OrgID: this.formdata.OrgID,
									Kd_Sub_Organisasi: this.formdata.Kd_Sub_Organisasi,
									Nm_Sub_Organisasi: this.formdata.Nm_Sub_Organisasi,
									Alias_Sub_Organisasi: this.formdata.Alias_Sub_Organisasi,
									Alamat: this.formdata.Alamat,
									NamaKepalaUnitKerja: this.formdata.NamaKepalaUnitKerja,
									NIPKepalaUnitKerja: this.formdata.NIPKepalaUnitKerja,
									Descr: this.formdata.Descr,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								if (data.unitkerja.Kd_Sub_Organisasi < 10) {
									data.unitkerja.Kd_Sub_Organisasi =
										"0" + data.unitkerja.Kd_Sub_Organisasi;
								}
								Object.assign(this.datatable[this.editedIndex], data.unitkerja);
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					} else {
						this.$ajax
							.post(
								"/dmaster/unitkerja/store/",
								{
									OrgID: this.formdata.OrgID,
									Kd_Sub_Organisasi: this.formdata.Kd_Sub_Organisasi,
									Nm_Sub_Organisasi: this.formdata.Nm_Sub_Organisasi,
									Alias_Sub_Organisasi: this.formdata.Alias_Sub_Organisasi,
									Alamat: this.formdata.Alamat,
									NamaKepalaUnitKerja: this.formdata.NamaKepalaUnitKerja,
									NIPKepalaUnitKerja: this.formdata.NIPKepalaUnitKerja,
									Descr: this.formdata.Descr,
									TA: this.$store.getters["auth/TahunSelected"],
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
					}
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data Unit Kerja dengan ID " +
							item.SOrgID +
							" ?",
						{ color: "red", width: "500px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/unitkerja/" + item.SOrgID,
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
			closedialogdetailitem() {
				this.btnLoading = false;
				this.dialogdetailitem = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
				}, 300);
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
