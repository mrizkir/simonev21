<template>
	<RPJMDLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-cogs
			</template>
			<template v-slot:name>
				PENGATURAN RPJMD
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
					Pengaturan Rencana Pembangunan Jangka Menengah Daerah
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						:search="search"
						item-key="ID"
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
								<v-toolbar-title>DAFTAR PENGATURAN RPJMD</v-toolbar-title>
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
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>TAMBAH PENGATURAN</span>
								</v-tooltip>
								<v-dialog v-model="dialogfrm" max-width="600px" persistent v-if="dialogfrm">
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">
													TAMBAH PENGATURAN
												</span>
											</v-card-title>							
											<v-card-text>
												<v-row>
													<v-col cols="6">
														<v-text-field
															label="AWAL PERIODE RPJMD"
															v-model="formdata.periode_awal"
															filled
															outlined
															hide-details="auto"
															dense
															class="mb-2"
															:rules="rule_periode_awal"
														/>
													</v-col>
													<v-col cols="6">
														<v-text-field
															label="AKHIR PERIODE RPJMD"
															v-model="formdata.periode_akhir"
															filled
															outlined
															hide-details="auto"
															dense
															class="mb-2"
															:rules="rule_periode_akhir"
														/>
													</v-col>
												</v-row>
												<v-text-field
													label="BUPATI"
													v-model="formdata.nama_bupati"
													filled
													outlined
													hide-details="auto"
													dense
													class="mb-2"
													:rules="rule_nama_bupati"
												/>
												<v-text-field
													label="WAKIL BUPATI"
													v-model="formdata.nama_wabup"
													filled
													outlined
													hide-details="auto"
													dense
													class="mb-2"
													:rules="rule_nama_wabup"
												/>
												<v-switch
													v-model="formdata.status"
													label="AKTIF"
                        >
												</v-switch>
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
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
	</RPJMDLayout>
</template>
<script>
	import RPJMDLayout from "@/views/layouts/RPJMDLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "PengaturanRPJMD",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "RPJMD",
					disabled: false,
					href: "/rpjmd",
				},
				{
					text: "PENGATURAN RPJMD",
					disabled: true,
					href: "#",
				},
			];
		},
		data: () => ({
			breadcrumbs: null,
			firstloading: true,
			expanded: [],
			search: "",
			btnLoading: false,
			datatableLoading: false,
			datatableLoaded: false,
			datatable: [],
			headers: [
				{ text: "PERIODE RPJMD", value: "periode_awal", width: 120, sortable: false, },
				{ text: "BUPATI", value: "bupati", width: 200, sortable: false, },
				{ text: "WAKIL BUPATI", value: "wakilbupati", width: 200, sortable: false, },
				{ text: "STATUS", value: "status", width: 70, sortable: false, },
				{ text: "AKSI", value: "actions", sortable: false, width: 110 },
			],
			//dialog
			dialogfrm: false,
			//form data
			form_valid: true,
			formdata: {
				periode_awal: null,
				periode_akhir: null,
				nama_bupati: null,
				nama_wabup: null,
				status: false,
			},
			formdefault: {
				periode_awal: null,
				periode_akhir: null,
				nama_bupati: null,
				nama_wabup: null,
				status: false,
			},
			editedIndex: -1,
			//form rules
			rule_periode_awal: [
				value => !!value || "Mohon untuk di isi awal periode RPJMD !!!",
				value => /^[0-9]+$/.test(value) || "Awal periode RPJMD hanya boleh angka",
			],
			rule_periode_akhir: [
				value => !!value || "Mohon untuk di isi akhir periode RPJMD !!!",
				value => /^[0-9]+$/.test(value) || "Akhir periode RPJMD hanya boleh angka",
			],
			rule_nama_bupati: [value => !!value || "Mohon untuk di isi Nama Bupati !!!"],
			rule_nama_wabup: [value => !!value || "Mohon untuk di isi Nama Wakil Bupati !!!"],
		}),
		methods: {
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
			save() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					if (!(this.editedIndex > -1)) {
						this.$ajax
							.post(
								"/rpjmd/pengaturan/store",
								{
									periode_awal: this.formdata.periode_awal,
									periode_akhir: this.formdata.periode_akhir,
									nama_bupati: this.formdata.nama_bupati,
									nama_wabup: this.formdata.nama_wabup,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								this.datatable.push(data.ta);
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					}
				}
			},		
			closedialogfrm() {
				this.btnLoading = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault)
					this.dialogfrm = false;
				}, 300);
			},
		},
		components: {
			RPJMDLayout,
			ModuleHeader,
		},
	};
</script>