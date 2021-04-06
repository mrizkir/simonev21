<template>
	<DataMasterLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-group
			</template>
			<template v-slot:name>
				PROGRAM
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
					Daftar "program" sesuai dengan Keputusan Menteri Dalam Negeri No.
					050-3708 tentang pemutakhiran, klasifikasi,
					kodefikasi, perencanaan,
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
						item-key="PrgID"
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
								<v-toolbar-title>DAFTAR PROGRAM</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-btn
									color="primary"
									class="mb-2"
									v-on="on"
									@click.stop="addItem"
									:disabled="
										!$store.getters['auth/can'](
											'DMASTER-KODEFIKASI-PROGRAM_STORE'
										)
									"
								>
									TAMBAH
								</v-btn>
								<v-dialog v-model="dialogfrm" max-width="800px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													{{ formtitle }}
												</span>
											</v-card-title>
											<v-card-text>
												<v-container fluid>
													<v-row>
														<v-col cols="12" sm="12" md="12">
															<v-select
																v-model="formdata.BidangID"
																:items="daftar_bidang_urusan"
																item-text="Nm_Bidang"
																item-value="BidangID"
																label="BIDANG URUSAN"
																:rules="rule_urusan"
																single-line
																filled
															>
															</v-select>
															<v-text-field
																v-model="formdata.Kd_Program"
																label="KODE PROGRAM"
																filled
																:rules="rule_kode"
															>
															</v-text-field>
														</v-col>
														<v-col cols="12" sm="12" md="12">
															<v-text-field
																v-model="formdata.Nm_Program"
																label="NAMA PROGRAM"
																filled
																:rules="rule_name"
															>
															</v-text-field>
														</v-col>
														<v-col cols="12" sm="12" md="12">
															<v-textarea
																v-model="formdata.Descr"
																label="KETERANGAN"
																filled
															>
															</v-textarea>
														</v-col>
													</v-row>
												</v-container>
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
											<span class="headline">
												DETAIL DATA
											</span>
										</v-card-title>
										<v-card-text>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															BidangID
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.BidangID }}
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
															KODE PROGRAM
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.kode_bidang }}
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
															NAMA PROGRAM
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
							</v-toolbar>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-icon small class="mr-2" @click.stop="viewItem(item)">
								mdi-eye
							</v-icon>
							<v-icon
								small
								class="mr-2"
								:disabled="
									!$store.getters['auth/can'](
										'DMASTER-KODEFIKASI-PROGRAM_UPDATE'
									)
								"
								@click.stop="editItem(item)"
							>
								mdi-pencil
							</v-icon>
							<v-icon
								small
								:disabled="
									btnLoading ||
										!$store.getters['auth/can'](
											'DMASTER-KODEFIKASI-PROGRAM_STORE'
										)
								"
								@click.stop="deleteItem(item)"
							>
								mdi-delete
							</v-icon>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>{{ item.BidangID }}
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
		name: "KodefikasiBidangUrusan",
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
					text: "PROGRAM",
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
					{ text: "KODE PROGRAM", value: "kode_program", width: 150 },
					{ text: "NAMA PROGRAM", value: "Nm_Program" },
					{ text: "BIDANG URUSAN", value: "Nm_Program" },
					{ text: "KET", value: "Descr" },
					{ text: "TA", value: "TA" },
					{ text: "AKSI", value: "actions", srotable: false, width: 100 },
				],
				search: "",
				//dialog
				dialogfrm: false,
				dialogdetailitem: false,
				//form data
				form_valid: true,
				daftar_bidang_urusan: [],
				formdata: {
					PrgID: "",
					BidangID: "",
					Kd_Program: "",
					Nm_Program: "",
					Jns: 1,
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				formdefault: {
					PrgID: "",
					BidangID: "",
					Kd_Program: "",
					Nm_Program: "",
					Jns: 1,
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				editedIndex: -1,
				//form rules
				rule_urusan: [
					value => !!value || "Mohon untuk di pilih Jenis Jabatan !!!",
				],
				rule_kode: [
					value => !!value || "Mohon untuk di isi Kode Bidang Urusan!!!",
					value =>
						/^[0-9]+$/.test(value) || "Kode Bidang Urusan hanya boleh angka",
					value => value.length > 1 || "Kode Bidang minimaml 2 angka",
				],
				rule_name: [
					value => !!value || "Mohon untuk di isi Nama Bidang Urusan !!!",
					value =>
						/^[A-Za-z\s\\,\\.]*$/.test(value) ||
						"Nama Bidang Urusan hanya boleh string dan spasi",
				],
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/dmaster/kodefikasi/program",
						{
							TA: this.$store.getters["uifront/getTahunAnggaran"],
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.kodefikasibidangurusan;
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
						"/dmaster/kodefikasi/bidangurusan",
						{
							TA: this.$store.getters["uifront/getTahunAnggaran"],
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_bidang_urusan = data.kodefikasibidangurusan;
						this.dialogfrm = true;
					});
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				await this.$ajax
					.post(
						"/dmaster/kodefikasi/program",
						{
							TA: this.$store.getters["uifront/getTahunAnggaran"],
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.daftar_bidang_urusan = data.kodefikasibidangurusan;
						this.formdata = Object.assign({}, item);
						this.dialogfrm = true;
					});
			},
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					if (this.editedIndex > -1) {
						this.$ajax
							.post(
								"/dmaster/kodefikasi/program/" + this.formdata.BidangID,
								{
									_method: "PUT",
									BidangID: this.formdata.BidangID,
									Kd_Program: this.formdata.Kd_Program,
									Nm_Program: this.formdata.Nm_Program,
									Descr: this.formdata.Descr,
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
								"/dmaster/kodefikasi/program/store",
								{
									BidangID: this.formdata.BidangID,
									Kd_Program: this.formdata.Kd_Program,
									Nm_Program: this.formdata.Nm_Program,
									Descr: this.formdata.Descr,
									TA: this.$store.getters["uifront/getTahunAnggaran"],
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
						"Apakah Anda ingin menghapus data bidang urusan dengan ID " +
							item.BidangID +
							" ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/kodefikasi/program/" + item.BidangID,
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
