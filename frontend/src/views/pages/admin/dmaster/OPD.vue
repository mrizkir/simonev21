<template>
	<DataMasterLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-office-building
			</template>
			<template v-slot:name>
				ORG. PERANGKAT DAERAH (OPD)
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
					Daftar OPD yang datanya diload dari SIMDA. Silahkan klik tombol load
					data.
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
						item-key="OrgID"
						sort-by="kode_organisasi"
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
									DAFTAR OPD
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
												!$store.getters['auth/can']('DMASTER-OPD_STORE')
											"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>Tambah Perangkat Daerah (OPD)</span>
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
													:items="daftar_bidang_urusan"
													v-model="bidangid_1"
													label="KODE BIDANG URUSAN 1"
													item-text="bidangurusan"
													item-value="BidangID"
													single-line
													filled
													return-object
												>
												</v-autocomplete>
												<v-autocomplete
													:items="daftar_bidang_urusan"
													v-model="bidangid_2"
													label="KODE BIDANG URUSAN 2"
													item-text="bidangurusan"
													item-value="BidangID"
													single-line
													filled
													return-object
												>
												</v-autocomplete>
												<v-autocomplete
													:items="daftar_bidang_urusan"
													v-model="bidangid_3"
													label="KODE BIDANG URUSAN 3"
													item-text="bidangurusan"
													item-value="BidangID"
													single-line
													filled
													return-object
												>
												</v-autocomplete>
												<v-text-field
													v-model="formdata.Kd_Organisasi"
													label="KODE OPD"
													:rules="rule_kode"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Nm_Organisasi"
													label="NAMA OPD"
													:rules="rule_required"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.Alias_Organisasi"
													:rules="rule_required"
													label="SINGKATAN OPD"
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
													v-model="formdata.NamaKepalaSKPD"
													:rules="rule_kepala_skpd"
													label="NAMA KEPALA OPD"
													filled
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.NIPKepalaSKPD"
													:rules="rule_nip_kepala_skpd"
													label="NIP KEPALA OPD"
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
											<span class="headline">DETAIL DATA OPD</span>
										</v-card-title>
										<v-card-text>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>
															<strong>ID:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.OrgID }}
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
															<strong>KODE OPD:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.kode_organisasi }}
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
															<strong>NAMA OPD:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.Nm_Organisasi }}
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
															{{ formdata.Alias_Organisasi }}
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
															{{ formdata.Nm_Bidang_1 }} {{ formdata.Nm_Bidang_2 }} {{ formdata.Nm_Bidang_3 }}
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
															<strong>KEPALA OPD:</strong>
														</v-card-title>
														<v-card-subtitle>
															{{ formdata.NamaKepalaSKPD }}
															NIP. {{ formdata.NIPKepalaSKPD }}
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
							{{ item.Nm_Bidang_1 }} {{ item.Nm_Bidang_2 }} {{ item.Nm_Bidang_3 }}
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
								<span>Detail OPD</span>
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
											!$store.getters['auth/can']('DMASTER-OPD_UPDATE')
										"
									>
										mdi-pencil
									</v-icon>
								</template>
								<span>Ubah OPD</span>
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
												!$store.getters['auth/can']('DMASTER-OPD_DESTROY')
										"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus OPD</span>
							</v-tooltip>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>OrgID:</strong>
								{{ item.OrgID }}
								<strong>Urusan:</strong>
								{{ item.Nm_Urusan }}
								<strong>ALIAS:</strong>
								{{ item.OrgAlias }}
								<strong>Alamat:</strong>
								{{ item.Alamat }}
								<strong>TA:</strong>
								{{ item.TA }}
								<strong>created_at:</strong>
								{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
								<strong>updated_at:</strong>
								{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
							</td>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<strong>ID:</strong>{{ item.OrgID }}
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
								<v-btn
									color="primary"
									@click.stop="loaddataopd"
									:disabled="showBtnLoadDataOPD || btnLoading"
								>
									LOAD DATA OPD
									<template v-slot:loader>
										<span>LOADING ...</span>
									</template>
								</v-btn>
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
		name: "OPD",
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
					text: "OPD",
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
					{ text: "KODE OPD", value: "kode_organisasi", width: 150 },
					{ text: "NAMA OPD", value: "Nm_Organisasi", width: 300 },
					{ text: "BIDANG URUSAN", value: "Nm_Bidang_1", width: 200 },
					{ text: "KEPALA OPD", value: "NamaKepalaSKPD", width: 200 },
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
				daftar_bidang_urusan: [],
				bidangid_1: null,
				bidangid_2: null,
				bidangid_3: null,
				formdata: {
					OrgID: "",
					BidangID_1: null,
					kode_bidang_1: null,
					Nm_Bidang_1: null,
					BidangID_2: null,
					kode_bidang_2: null,
					Nm_Bidang_2: null,
					BidangID_3: null,
					kode_bidang_3: null,
					Nm_Bidang_3: null,
					kode_organisasi: "",
					Kd_Organisasi: "",
					Nm_Organisasi: "",
					Alias_Organisasi: "",
					Alamat: "",
					NamaKepalaSKPD: "",
					NIPKepalaSKPD: "",
					PaguDana1: 0,
					PaguDana2: 0,
					Descr: "",
					TA: "",
					created_at: "",
					updated_at: "",
				},
				formdefault: {
					OrgID: "",
					BidangID_1: "",
					kode_bidang_1: "",
					Nm_Bidang_1: "",
					BidangID_2: "",
					kode_bidang_2: "",
					Nm_Bidang_2: "",
					BidangID_3: "",
					kode_bidang_3: "",
					Nm_Bidang_3: "",
					kode_organisasi: "",
					Kd_Organisasi: "",
					Nm_Organisasi: "",
					Alias_Organisasi: "",
					Alamat: "",
					NamaKepalaSKPD: "",
					NIPKepalaSKPD: "",
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
					value => !!value || "Mohon untuk di isi Kode OPD!!!",
					value => /^[0-9]+$/.test(value) || "Kode OPD hanya boleh angka",
					value => {
						if (value && typeof value !== "undefined" && value.length >= 2){
							return true;
						} else {
							return "Kode OPD Kegiatan minimaml 2 angka";
						}     
					}
				],
				rule_kepala_skpd: [
					value => !!value || "Mohon untuk di isi nama Kepala OPD / SKPD !!!",
					value =>
						/^[A-Za-z\s\\,\\.]*$/.test(value) ||
						"Nama Kepala OPD / SKPD hanya boleh string dan spasi",
				],
				rule_nip_kepala_skpd: [
					value => !!value || "Mohon untuk di isi NIP Kepala OPD / SKPD !!!",
					value =>
						/^[0-9]+$/.test(value) || "NIP Kepala OPD / SKPD hanya boleh angka",
				],
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
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
						var opd = data.opd;
						for (var key in opd) {
							if (opd[key].Kd_Organisasi < 10) {
								opd[key].Kd_Organisasi = "0" + opd[key].Kd_Organisasi;
							}
						}
						this.datatable = data.opd;
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
			loaddataopd() {
				this.$root.$confirm
					.open(
						"Load Data OPD",
						"Apakah Anda ingin meload data OPD kembali ?",
						{ color: "yellow" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/opd/loadopd",
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
									this.datatable = data.opd;
									this.footers.jumlah_apbd = data.jumlah_apbd;
									this.footers.jumlah_apbdp = data.jumlah_apbdp;
									this.btnLoading = false;
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			loadPaguAPBDP() {
				this.btnLoading = true;
				this.$ajax
					.post(
						"/dmaster/opd/loadpaguapbdp",
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
						this.datatable = data.opd;
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
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.formdata = Object.assign({}, item);
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
						if (this.formdata.BidangID_1) {
							this.bidangid_1 = {
								BidangID: this.formdata.BidangID_1,
								kode_bidang: this.formdata.kode_bidang_1,
								Nm_Bidang: this.formdata.Nm_Bidang_1,
							};
						}
						if (this.formdata.BidangID_2) {
							this.bidangid_2 = {
								BidangID: this.formdata.BidangID_2,
								kode_bidang: this.formdata.kode_bidang_2,
								Nm_Bidang: this.formdata.Nm_Bidang_2,
							};
						}
						if (this.formdata.BidangID_3) {
							this.bidangid_3 = {
								BidangID: this.formdata.BidangID_3,
								kode_bidang: this.formdata.kode_bidang_3,
								Nm_Bidang: this.formdata.Nm_Bidang_3,
							};
						}
						this.dialogfrm = true;
					});				
			},
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					var org1 = "0-00";
					var kode_bidang = "";
					if (this.bidangid_1) {
						kode_bidang = this.bidangid_1.kode_bidang;
						kode_bidang = kode_bidang.replace(".", "-");
						this.formdata.BidangID_1 = this.bidangid_1.BidangID;
						this.formdata.kode_bidang_1 = kode_bidang;
						this.formdata.Nm_Bidang_1 = this.bidangid_1.Nm_Bidang;
						org1 = kode_bidang;
					}
					var org2 = "0-00";
					if (this.bidangid_2) {
						kode_bidang = this.bidangid_2.kode_bidang;
						kode_bidang = kode_bidang.replace(".", "-");
						this.formdata.BidangID_2 = this.bidangid_2.BidangID;
						this.formdata.kode_bidang_2 = kode_bidang;
						this.formdata.Nm_Bidang_2 = this.bidangid_2.Nm_Bidang;

						org2 = kode_bidang;
					}
					var org3 = "0-00";
					if (this.bidangid_3) {
						kode_bidang = this.bidangid_3.kode_bidang;
						kode_bidang = kode_bidang.replace(".", "-");
						this.formdata.BidangID_3 = this.bidangid_3.BidangID;
						this.formdata.kode_bidang_3 = kode_bidang;
						this.formdata.Nm_Bidang_3 = this.bidangid_3.Nm_Bidang;

						org3 = kode_bidang;
					}
					this.formdata.kode_organisasi =
						org1 + "." + org2 + "." + org3 + "." + this.formdata.Kd_Organisasi;
					if (this.editedIndex > -1) {
						this.$ajax
							.post(
								"/dmaster/opd/" + this.formdata.OrgID,
								{
									_method: "PUT",
									BidangID_1: this.formdata.BidangID_1,
									kode_bidang_1: this.formdata.kode_bidang_1,
									Nm_Bidang_1: this.formdata.Nm_Bidang_1,
									BidangID_2: this.formdata.BidangID_2,
									kode_bidang_2: this.formdata.kode_bidang_2,
									Nm_Bidang_2: this.formdata.Nm_Bidang_2,
									BidangID_3: this.formdata.BidangID_3,
									kode_bidang_3: this.formdata.kode_bidang_3,
									Nm_Bidang_3: this.formdata.Nm_Bidang_3,
									kode_organisasi: this.formdata.kode_organisasi,
									Kd_Organisasi: this.formdata.Kd_Organisasi,
									Nm_Organisasi: this.formdata.Nm_Organisasi,
									Alias_Organisasi: this.formdata.Alias_Organisasi,
									Alamat: this.formdata.Alamat,
									NamaKepalaSKPD: this.formdata.NamaKepalaSKPD,
									NIPKepalaSKPD: this.formdata.NIPKepalaSKPD,
									Descr: this.formdata.Descr,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								Object.assign(this.datatable[this.editedIndex], data.opd);
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					} else {
						this.$ajax
							.post(
								"/dmaster/opd/store/",
								{
									BidangID_1: this.formdata.BidangID_1,
									kode_bidang_1: this.formdata.kode_bidang_1,
									Nm_Bidang_1: this.formdata.Nm_Bidang_1,
									BidangID_2: this.formdata.BidangID_2,
									kode_bidang_2: this.formdata.kode_bidang_2,
									Nm_Bidang_2: this.formdata.Nm_Bidang_2,
									BidangID_3: this.formdata.BidangID_3,
									kode_bidang_3: this.formdata.kode_bidang_3,
									Nm_Bidang_3: this.formdata.Nm_Bidang_3,
									kode_organisasi: this.formdata.kode_organisasi,
									Kd_Organisasi: this.formdata.Kd_Organisasi,
									Nm_Organisasi: this.formdata.Nm_Organisasi,
									Alias_Organisasi: this.formdata.Alias_Organisasi,
									Alamat: this.formdata.Alamat,
									NamaKepalaSKPD: this.formdata.NamaKepalaSKPD,
									NIPKepalaSKPD: this.formdata.NIPKepalaSKPD,
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
						"Apakah Anda ingin menghapus data OPD dengan ID " +
							item.OrgID +
							" ?",
						{ color: "red", width: "500px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/dmaster/opd/" + item.OrgID,
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
			showBtnLoadDataOPD() {
				var bool = true;
				if (this.datatableLoaded == true) {
					bool = this.datatable.length > 0;
				}
				return bool;
			},
		},
		components: {
			DataMasterLayout,
			ModuleHeader,
		},
	};
</script>
